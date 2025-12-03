@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-attendance-list.css') }}">
@endsection

@section('content')
<div class="content-title">
    <span>{{ $currentDate->format('Y年n月j日') }} の勤務</span>
</div>

<div class="list-content">
    <div class="day-navigation">
        <a href="{{ route('admin.attendance.list', ['date' => $prevDate->format('Y-m-d')]) }}" class="day-nav-link">
            <img src="{{ asset('images/left-arrow.png') }}" alt="前日" class="day-arrow">
            <span class="day-text">前日</span></a>
        <div class="day-display">
            <label for="datePicker" class="calendar-icon">📅</label>
            <input type="date" id="datePicker" style="display:none;" value="{{ $currentDate->format('Y-m-d') }}">
            <span id="currentDay">{{ $currentDate->format('Y/m/d') }}</span>
        </div>
        <a href="{{ route('admin.attendance.list', ['date' => $nextDate->format('Y-m-d')]) }}" class="day-nav-link">
            <span class="day-text">翌日</span>
            <img src="{{ asset('images/right-arrow.png') }}" alt="翌日" class="day-arrow"></a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->user->name }}</td>
                    <td>{{ $attendance->clock_in_hhmm }}</td>
                    <td>{{ $attendance->clock_out_hhmm }}</td>
                    <td>{{ $attendance->total_break_hhmm }}</td>
                    <td>{{ $attendance->total_work_hhmm }}</td>
                    <td>
                        <a href="{{ url('admin/attendance/detail') }}?user_id={{ $attendance->user_id }}&date={{ $currentDate->format('Y-m-d') }}">
                            詳細
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5"></td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const datePicker = document.getElementById('datePicker');
        const currentDay = document.getElementById('currentDay');

        document.querySelector('.calendar-icon').addEventListener('click', function () {
            datePicker.showPicker();
        });

        datePicker.addEventListener('change', function () {
            const selectedDate = this.value;
            if (!selectedDate) return;

            window.location.href = `{{ route('admin.attendance.list') }}?date=${selectedDate}`;
        });
    });
    </script>
</div>
@endsection

