@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/staff-attendance.css') }}">
@endsection

@section('content')
<div class="content-title">
    <h2>{{ $staff->name }}さんの勤怠（{{ $year }}年 {{ $month }}月）</h2>
</div>

<div class="list-content">
    <div class="month-navigation">
        <a href="{{ route('admin.attendance.staff', ['id' => $staff->id, 'year' => $prevYear, 'month' => $prevMonth ]) }}" class="month-nav-link">
            <img src="{{ asset('images/left-arrow.png') }}" alt="前月" class="month-arrow">
            <span class="month-text">前月</span>
        </a>

        <span class="current-month">
            <label for="monthPicker" class="calendar-icon" style="cursor:pointer;">📅</label>
            <input type="month" id="monthPicker" style="display:none;">
            {{ $year }}年 {{ $month }}月
        </span>

        <a href="{{ route('admin.attendance.staff', ['id' => $staff->id, 'year' => $nextYear, 'month' => $nextMonth ]) }}" class="month-nav-link">
            <span class="month-text">翌月</span>
            <img src="{{ asset('images/right-arrow.png') }}" alt="翌月" class="month-arrow">
        </a>
    </div>

    <div class="attendance-list">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($attendanceList as $item)
                @php
                    $attendance = $item['attendance'];
                    $date = $item['work_date'];
                    $dateObj = \Carbon\Carbon::parse($date);
                    $weekday = ['日','月','火','水','木','金','土'][$dateObj->dayOfWeek];

                    $start = $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time) : null;
                    $end   = $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time) : null;

                    $breakMinutes = 0;
                    if ($attendance->break_start && $attendance->break_end) {
                        $breakMinutes += \Carbon\Carbon::parse($attendance->break_end)
                            ->diffInMinutes(\Carbon\Carbon::parse($attendance->break_start));
                    }
                    if ($attendance->break2_start && $attendance->break2_end) {
                        $breakMinutes += \Carbon\Carbon::parse($attendance->break2_end)
                            ->diffInMinutes(\Carbon\Carbon::parse($attendance->break2_start));
                    }

                    $workMinutes = ($start && $end) ? $end->diffInMinutes($start) - $breakMinutes : 0;

                    $breakTime = $breakMinutes > 0 ? sprintf('%02d:%02d', intdiv($breakMinutes, 60), $breakMinutes % 60) : '';
                    $totalTime = $workMinutes > 0 ? sprintf('%02d:%02d', intdiv($workMinutes, 60), $workMinutes % 60) : '';
                @endphp

                    <tr>
                        <td>{{ $dateObj->format('m/d') }} ({{ $weekday }})</td>
                        <td>{{ $attendance->clock_in_hhmm }}</td>
                        <td>{{ $attendance->clock_out_hhmm }}</td>
                        <td>{{ $breakTime }}</td>
                        <td>{{ $totalTime }}</td>
                        <td>
                            <a href="{{ route('admin.attendance.detail', [
                            'user_id' => $staff->id, 'date' => $date]) }}">
                                詳細
                            </a>
                        </td>
                    </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="csv-export">
        <a href="{{ route('admin.attendance.csv', ['id' => $staff->id, 'year' => $year, 'month' => $month]) }}" class="csv-button" target="_blank">
            CSV出力
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const monthPicker = document.getElementById('monthPicker');
    const calendarIcon = document.querySelector('.calendar-icon');

    calendarIcon.addEventListener('click', () => monthPicker.showPicker());

    monthPicker.addEventListener('change', (e) => {
        const [year, month] = e.target.value.split('-');
        const staffId = {{ $staff->id }};
        window.location.href = `/admin/attendance/staff/${staffId}?year=${year}&month=${month}`;
    });
});
</script>
@endsection