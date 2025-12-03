<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Attendance;
use App\Models\User;
use App\Http\Requests\AdminAttendanceUpdateRequest;

class AdminAttendanceController extends Controller
{
    public function list(Request $request)
    {
        $date = $request->query('date') ? Carbon::parse($request->query('date')) : Carbon::today();

        $attendances = Attendance::with('user')
            ->where('work_date', $date->toDateString())
            ->whereNotNull('start_time')
            ->get();

        return view('admin.attendance.list', [
            'attendances' => $attendances,
            'currentDate' => $date,
            'prevDate' => $date->copy()->subDay(),
            'nextDate' => $date->copy()->addDay(),
        ]);
    }

    public function detail(Request $request)
    {
        $userId = $request->query('user_id');
        $date   = $request->query('date');

        if (!$userId) {
            abort(404);
        }

        if (!$date) {
            $date = now()->toDateString();
        }

        $attendance = Attendance::firstOrNew([
            'user_id'   => $userId,
            'work_date' => $date,
        ]);

        $isPending = $attendance->approval_status === '承認待ち';

        return view('admin.attendance.detail', compact('attendance', 'isPending', 'userId', 'date'));
    }

    public function update(AdminAttendanceUpdateRequest $request)
    {
        $workDate = Carbon::parse($request->work_date)->toDateString();

        $attendance = Attendance::firstOrNew([
            'user_id' => $request->user_id,
            'work_date' => $workDate,
        ]);

        $attendance->start_time   = $request->start_time   ? Carbon::parse($request->start_time)   : null;
        $attendance->end_time     = $request->end_time     ? Carbon::parse($request->end_time)     : null;
        $attendance->break_start  = $request->break_start  ? Carbon::parse($request->break_start)  : null;
        $attendance->break_end    = $request->break_end    ? Carbon::parse($request->break_end)    : null;
        $attendance->break2_start = $request->break2_start ? Carbon::parse($request->break2_start) : null;
        $attendance->break2_end   = $request->break2_end   ? Carbon::parse($request->break2_end)   : null;

        $attendance->note = $request->note;

        $attendance->approval_status = '承認済';

        $attendance->save();

        return redirect()->back();
    }

    public function staffMonthly($id, Request $request)
    {
        $staff = User::findOrFail($id);

        $year  = $request->query('year', now()->year);
        $month = $request->query('month', now()->month);

        $firstDay = Carbon::create($year, $month, 1);
        $lastDay  = $firstDay->copy()->endOfMonth();

        $days = CarbonPeriod::create($firstDay, $lastDay);

        $attendances = Attendance::where('user_id', $staff->id)
            ->whereBetween('work_date', [$firstDay, $lastDay])
            ->get()
            ->keyBy(fn ($attendance) => $attendance->work_date->format('Y-m-d'));

        $attendanceList = [];

        foreach ($days as $day) {
            $date = $day->toDateString();

            $attendance = $attendances->get($date);

            if (!$attendance) {

                $attendance = new Attendance([
                    'user_id' => $staff->id,
                    'work_date' => $date,
                    'start_time' => null,
                    'end_time' => null,
                    'break_start' => null,
                    'break_end' => null,
                    'break2_start' => null,
                    'break2_end' => null,
                    'status' => '勤務外',
                ]);
                $attendance->is_fake = true;
            } else {
                $attendance->is_fake = false;
            }


            $attendance->clock_in_hhmm  = $attendance->start_time ? Carbon::parse($attendance->start_time)->format('H:i') : '';
            $attendance->clock_out_hhmm = $attendance->end_time ? Carbon::parse($attendance->end_time)->format('H:i') : '';

            $totalBreakMinutes = 0;
            if ($attendance->break_start && $attendance->break_end) {
                $totalBreakMinutes += Carbon::parse($attendance->break_start)->diffInMinutes(Carbon::parse($attendance->break_end));
            }
            if ($attendance->break2_start && $attendance->break2_end) {
                $totalBreakMinutes += Carbon::parse($attendance->break2_start)->diffInMinutes(Carbon::parse($attendance->break2_end));
            }

            $attendance->total_break_hhmm = $totalBreakMinutes > 0
                ? sprintf('%02d:%02d', intdiv($totalBreakMinutes, 60), $totalBreakMinutes % 60)
                : '';

            if ($attendance->start_time && $attendance->end_time) {
                $workMinutes = Carbon::parse($attendance->end_time)->diffInMinutes(Carbon::parse($attendance->start_time)) - $totalBreakMinutes;
                $workMinutes = max($workMinutes, 0);
                $attendance->total_work_hhmm = sprintf('%02d:%02d', intdiv($workMinutes, 60), $workMinutes % 60);
            } else {
                $attendance->total_work_hhmm = '';
            }

            $attendanceList[] = [
                'work_date' => $date,
                'attendance' => $attendance
            ];
        }

            return view('admin.staff.attendance', [
            'staff' => $staff,
            'attendanceList' => $attendanceList,
            'year' => $year,
            'month' => $month,
            'prevYear' => $firstDay->copy()->subMonth()->year,
            'prevMonth' => $firstDay->copy()->subMonth()->month,
            'nextYear' => $firstDay->copy()->addMonth()->year,
            'nextMonth' => $firstDay->copy()->addMonth()->month,
        ]);
    }

    public function staffMonthlyCsv($id, Request $request)
    {
        $staff = User::findOrFail($id);
        $year = $request->year;
        $month = $request->month;

        $startOfMonth = Carbon::create($year, $month, 1);
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        $attendances = Attendance::where('user_id', $id)
            ->whereBetween('work_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('work_date')
            ->get()
            ->keyBy(function ($attendance) {
                return Carbon::parse($attendance->work_date)->format('Y-m-d');
            });

        $csvData = [];
        $csvData[] = ["{$staff->name} さんの勤怠（{$year}年 {$month}月）"];
        $csvData[] = [""];
        $csvData[] = ["日付", "出勤", "退勤", "休憩", "実働"];

        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);

        foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                $attendance = $attendances[$dateStr] ?? null;

            if ($attendance) {

                $clockIn  = $attendance->start_time ? Carbon::parse($attendance->start_time)->format('H:i') : '';
                $clockOut = $attendance->end_time   ? Carbon::parse($attendance->end_time)->format('H:i') : '';

                $breakMinutes = 0;
                if ($attendance->break_start && $attendance->break_end) {
                    $breakMinutes += Carbon::parse($attendance->break_end)
                        ->diffInMinutes(Carbon::parse($attendance->break_start));
                }
                if ($attendance->break2_start && $attendance->break2_end) {
                    $breakMinutes += Carbon::parse($attendance->break2_end)
                        ->diffInMinutes(Carbon::parse($attendance->break2_start));
                }

                $breakTime = $breakMinutes > 0
                    ? sprintf('%02d:%02d', intdiv($breakMinutes, 60), $breakMinutes % 60)
                    : '';

                if ($attendance->start_time && $attendance->end_time) {
                    $workMinutes = Carbon::parse($attendance->end_time)->diffInMinutes(Carbon::parse($attendance->start_time)) - $breakMinutes;
                    $workMinutes = max($workMinutes, 0);
                    $totalTime = sprintf('%02d:%02d', intdiv($workMinutes, 60), $workMinutes % 60);
                } else {
                    $totalTime = '';
                }

            } else {
                $clockIn = $clockOut = $breakTime = $totalTime = '';
            }

            $csvData[] = [
                $date->format("m/d"),
                $clockIn,
                $clockOut,
                $breakTime,
                $totalTime,
            ];
        }

        $filename = "attendance_{$staff->id}_{$year}{$month}.csv";
        $output = fopen('php://temp', 'r+');
        foreach ($csvData as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }
}