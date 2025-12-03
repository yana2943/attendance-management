<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Http\Requests\AttendanceDetailRequest;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'work_date' => $today],
            ['user_status' => '勤務外', 'approval_status' => '承認待ち']
        );

        return view('attendance', ['attendance' => $attendance, 'status' => $attendance->user_status,]);
    }

    public function toggleWork()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'work_date' => $today],
            ['user_status' => '勤務外', 'approval_status' => '承認待ち']
        );

        if ($attendance->user_status === '勤務外') {
            $attendance->start_time = now();
            $attendance->user_status = '出勤中';
        } elseif (in_array($attendance->user_status, ['出勤中', '休憩中'])) {
            $attendance->end_time = now();
            $attendance->user_status = '退勤済';
        }

        $attendance->save();
        return redirect()->route('attendance.index');
    }

    public function toggleBreak()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if (!$attendance) return redirect()->route('attendance.index');

        if ($attendance->user_status === '出勤中') {
            if (is_null($attendance->break_start)) {
                $attendance->break_start = now();
            } elseif (!is_null($attendance->break_start) && !is_null($attendance->break_end) && is_null($attendance->break2_start)) {
                $attendance->break2_start = now();
            }
            $attendance->user_status = '休憩中';
        } elseif ($attendance->user_status === '休憩中') {
            if (!is_null($attendance->break_start) && is_null($attendance->break_end)) {
                $attendance->break_end = now();
            } elseif (!is_null($attendance->break2_start) && is_null($attendance->break2_end)) {
                $attendance->break2_end = now();
            }
            $attendance->user_status = '出勤中';
        }

        $attendance->save();
        return redirect()->route('attendance.index');
    }

    public function list(Request $request)
    {
        $user = Auth::user();
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $firstDay = Carbon::create($year, $month, 1);
        $lastDay  = $firstDay->copy()->endOfMonth();
        $days = CarbonPeriod::create($firstDay, $lastDay);

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('work_date', [$firstDay, $lastDay])
            ->get()
            ->keyBy(fn ($att) => $att->work_date->format('Y-m-d'));

        $attendanceList = [];

        foreach ($days as $day) {
            $date = $day->toDateString();
            $attendance = $attendances->get($date);

            if (!$attendance) {
                $attendance = new Attendance([
                    'user_id' => $user->id,
                    'work_date' => $date,
                    'user_status' => '勤務外',
                    'approval_status' => '承認待ち',
                ]);
                $attendance->is_fake = true;
            } else {
                $attendance->is_fake = false;
            }

            $attendanceList[] = ['work_date' => $date, 'attendance' => $attendance];
        }

        return view('attendance.list', [
            'attendanceList' => $attendanceList,
            'year' => $year,
            'month' => $month,
            'prevYear' => $firstDay->copy()->subMonth()->year,
            'prevMonth' => $firstDay->copy()->subMonth()->month,
            'nextYear' => $firstDay->copy()->addMonth()->year,
            'nextMonth' => $firstDay->copy()->addMonth()->month
        ]);
    }

    public function detail($id, Request $request)
    {
        $userId = $request->query('user_id') ?? auth()->id();

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $id)) {

            $attendance = Attendance::where('user_id', $userId)
                ->where('work_date', $id)
                ->first();

            if (!$attendance) {
                $attendance = new Attendance([
                    'user_id' => $userId,
                    'work_date' => $id,
                    'user_status' => '勤務外',
                    'approval_status' => '承認待ち',
                ]);
                $attendance->is_fake = true;
            } else {
                $attendance->is_fake = false;
            }

        } else {
            $attendance = Attendance::findOrFail($id);
            $attendance->is_fake = false;
        }

        $isPending = $attendance->approval_status === '承認待ち';

        return view('attendance.detail', compact('attendance', 'isPending'));
    }

    public function submitCorrection(AttendanceDetailRequest $request)
    {

        $workDate = Carbon::parse($request->work_date)->toDateString();

        $attendance = Attendance::firstOrNew([
            'user_id'   => $request->user_id,
            'work_date' => $workDate,
        ]);

        $attendance->start_time   = $request->start_time   ? Carbon::parse($request->start_time)   : null;
        $attendance->end_time     = $request->end_time     ? Carbon::parse($request->end_time)     : null;
        $attendance->break_start  = $request->break_start  ? Carbon::parse($request->break_start)  : null;
        $attendance->break_end    = $request->break_end    ? Carbon::parse($request->break_end)    : null;
        $attendance->break2_start = $request->break2_start ? Carbon::parse($request->break2_start) : null;
        $attendance->break2_end   = $request->break2_end   ? Carbon::parse($request->break2_end)   : null;

        $attendance->note = $request->note;

        $attendance->approval_status = '承認待ち';

        $attendance->save();

        return redirect()->back();
    }

    public function correctionsList(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status', 'pending');

        $query = Attendance::where('user_id', $user->id);

        if ($status === 'pending') {
            $query->where('approval_status', '承認待ち');
        } else {
            $query->where('approval_status', '承認済');
        }

        $corrections = $query->orderBy('work_date', 'desc')->get();

        return view('stamp_correction_request.list', compact('corrections', 'status'));
    }

    public function myCorrections(Request $request)
    {
        $status = $request->get('status', 'pending');
        $user = Auth::user();

        $query = Attendance::where('user_id', $user->id);

        if ($status === 'pending') {
            $query->where('approval_status', '承認待ち');
        } else {
            $query->where('approval_status', '承認済');
        }

        $corrections = $query->orderBy('work_date', 'desc')->get();

        return view('attendance.', compact('corrections', 'status'));
    }
}

