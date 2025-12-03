<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;

class AdminCorrectionController extends Controller
{
    public function list(Request $request)
    {
        $status = $request->query('status', 'pending');

        $corrections = Attendance::with('user')
            ->when($status === 'pending', fn($q) => $q->where('approval_status', '承認待ち'))
            ->when($status === 'approved', fn($q) => $q->where('approval_status', '承認済'))
            ->orderBy('work_date', 'desc')
            ->get();

        return view('admin.stamp_correction_request.list', compact('corrections', 'status'));
    }

    public function showApprove($id)
    {
        $attendance = Attendance::with('user')->findOrFail($id);
        return view('admin.approve', compact('attendance'));
    }

    public function approve(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $attendance->approval_status = '承認済';
        $attendance->save();

        return response()->json(['success' => true], 202);
    }
}