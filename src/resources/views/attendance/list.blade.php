@extends('layouts.common')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-list.css') }}">
@endsection

@section('content')
<div class="content-title">
    <h2>勤怠一覧</h2>
</div>

<div class="list-content">
    <div class="month-navigation">
        <a href="{{ route('attendance.list', ['year' => $prevYear, 'month' => $prevMonth]) }}" class="month-nav-link">
            <img src="{{ asset('images/left-arrow.png') }}" alt="前月" class="month-arrow">
            <span class="month-text">前月</span>
        </a>
        <div class="month-display">
            <label for="monthPicker" class="calendar-icon">📅</label>
            <input type="month" id="monthPicker" style="display:none;">
            <span id="currentMonth">{{ $year }}/{{ sprintf('%02d', $month) }}</span>
        </div>
        <a href="{{ route('attendance.list', ['year' => $nextYear, 'month' => $nextMonth]) }}" class="month-nav-link">
            <span class="month-text">翌月</span>
            <img src="{{ asset('images/right-arrow.png') }}" alt="翌月" class="month-arrow">
        </a>
    </div>
    <div class="attendance-table">
        <table>
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
                        $date = \Carbon\Carbon::parse($item['work_date']);
                        $weekday = ['日','月','火','水','木','金','土'][$date->dayOfWeek];
                    @endphp
                    <tr>
                        <td>{{ $date->format('m/d') }} ({{ $weekday }})</td>
                        <td>{{ $attendance->clock_in_hhmm ?? '' }}</td>
                        <td>{{ $attendance->clock_out_hhmm ?? '' }}</td>
                        <td>{{ ($attendance->total_break_hhmm === '00:00') ? '' : $attendance->total_break_hhmm }}</td>
                        <td>{{ $attendance->total_work_hhmm ?? '' }}</td>
                        <td>
                            @if(!$attendance->is_fake)
                                <a href="{{ route('attendance.detail', ['id' => $attendance->id]) }}" class="detail-link">詳細</a>
                            @else
                                <a href="{{ route('attendance.detail', ['id' => \Carbon\Carbon::parse($attendance->work_date)->toDateString(), 'user_id' => $attendance->user_id ]) }}" class="detail-link">詳細</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const monthPicker = document.getElementById('monthPicker');
    const currentMonth = document.getElementById('currentMonth');

    document.querySelector('.calendar-icon').addEventListener('click', () => {
        monthPicker.showPicker();
    });

    monthPicker.addEventListener('change', (e) => {
        const [year, month] = e.target.value.split('-');
        window.location.href = `/attendance/list?year=${year}&month=${month}`;
    });
});
</script>
@endsection