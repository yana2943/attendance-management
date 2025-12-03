@extends('layouts.common')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance-container">
    <div class="status-box">
        <span id="status-text">{{ $status }}</span>
    </div>
    <div class="time-box">
        <p id="date-part" class="date-text"></p>
        <p id="time-part" class="time-text"></p>
    </div>
    <div class="button-box">
    @if($status === '勤務外')
        <form action="{{ route('attendance.toggleWork') }}" method="POST">
            @csrf
            <button type="submit" class="work-btn">出勤</button>
        </form>
    @endif

    @if($status === '出勤中')
        <div class="work-break-row">
            <form action="{{ route('attendance.toggleWork') }}" method="POST">
                @csrf
                <button type="submit" class="work-btn">退勤</button>
            </form>
            <form action="{{ route('attendance.toggleBreak') }}" method="POST">
                @csrf
                <button type="submit" class="break-btn">休憩入</button>
            </form>
        </div>
    @endif

    @if($status === '休憩中')
        <form action="{{ route('attendance.toggleBreak') }}" method="POST">
            @csrf
            <button type="submit" class="break-btn">休憩戻り</button>
        </form>
    @endif

    @if ($status === '退勤済')
        <div class="message">
            <p>お疲れ様でした。</p>
        </div>
    @endif
    </div>
</div>


<script>
    function updateDateTime() {
        const now = new Date();
        const datePart = now.toLocaleDateString('ja-JP', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        weekday: 'short'
    });

    const timePart = now.toLocaleTimeString('ja-JP', {
        hour: '2-digit',
        minute: '2-digit'
    });

    document.getElementById('date-part').textContent = datePart;
    document.getElementById('time-part').textContent = timePart;
    }

    setInterval(updateDateTime, 1000);
    updateDateTime();
</script>
@endsection