@extends('layouts.common')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-detail.css') }}">
@endsection

@section('content')
<div class="content-title">
    <h2>勤怠詳細</h2>
</div>

@php
use Carbon\Carbon;
$correction = $attendance->correction ?? null;
$startTime   = old('start_time', $correction->start_time ?? $attendance->start_time);
$endTime     = old('end_time', $correction->end_time ?? $attendance->end_time);
$break1Start = old('break_start', $correction->break_start ?? $attendance->break_start);
$break1End   = old('break_end', $correction->break_end ?? $attendance->break_end);
$break2Start = old('break2_start', $correction->break2_start ?? $attendance->break2_start);
$break2End   = old('break2_end', $correction->break2_end ?? $attendance->break2_end);
$note        = old('note', $correction->note ?? $attendance->note);
$isPending = $attendance->approval_status === '承認待ち';
@endphp

<div class="attendance-detail">
    <form method="POST" action="{{ route('attendance.submitCorrection') }}">
    @csrf
        <input type="hidden" name="work_date" value="{{ $attendance->work_date }}">
        <input type="hidden" name="user_id" value="{{ $attendance->user_id }}">
        <div class="detail-item">
            <span class="label">名前</span>
            <span class="value">{{ $attendance->user->name ?? '' }}</span>
        </div>
        <div class="border"></div>
        <div class="detail-item">
            <span class="label">日付</span>
            <span class="value">{{ Carbon::parse($attendance->work_date)->format('Y年n月j日') }}</span>
        </div>
        <div class="border"></div>
        <div class="detail-item">
            <span class="label">出勤・退勤</span>
            <div class="time-input-group">
                <input type="text" name="start_time"
                    value="{{ $startTime ? Carbon::parse($startTime)->format('H:i') : '' }}"
                    @if($isPending) readonly @endif>
                <span class="middle-symbol">〜</span>
                <input type="text" name="end_time"
                    value="{{ $endTime ? Carbon::parse($endTime)->format('H:i') : '' }}"
                    @if($isPending) readonly @endif>
            </div>
        </div>
        <div class="form-error">
            @error('start_time') {{ $message }} @enderror
            @error('end_time') {{ $message }} @enderror
        </div>
        <div class="border"></div>
        <div class="detail-item">
            <span class="label">休憩</span>
            <div class="time-input-group">
                <input type="text" name="break_start"
                    value="{{ $break1Start ? Carbon::parse($break1Start)->format('H:i') : '' }}"
                    @if($isPending) readonly @endif>
                <span class="middle-symbol">〜</span>
                <input type="text" name="break_end"
                    value="{{ $break1End ? Carbon::parse($break1End)->format('H:i') : '' }}"
                    @if($isPending) readonly @endif>
            </div>
        </div>
        <div class="form-error">
            @error('break_start') {{ $message }} @enderror
            @error('break_end') {{ $message }} @enderror
        </div>
        <div class="border"></div>
        <div class="detail-item">
            <span class="label">休憩2</span>
            <div class="time-input-group">
                <input type="text" name="break2_start"
                    value="{{ $break2Start ? Carbon::parse($break2Start)->format('H:i') : '' }}"
                    @if($isPending) readonly @endif>
                <span class="middle-symbol">〜</span>
                <input type="text" name="break2_end"
                    value="{{ $break2End ? Carbon::parse($break2End)->format('H:i') : '' }}"
                    @if($isPending) readonly @endif>
            </div>
        </div>
        <div class="form-error">
                @error('break2_start') {{ $message }} @enderror
                @error('break2_end') {{ $message }} @enderror
        </div>
        <div class="border"></div>
        <div class="detail-item">
            <span class="label">備考</span>
            <textarea name="note" rows="3" @if($isPending) readonly @endif>{{ $note }}</textarea>
        </div>
        <div class="form-error">
            @error('note') {{ $message }} @enderror
        </div>
        <div class="detail-actions">
            @if(!$isPending)
                <button type="submit" class="edit-button">修正</button>
            @else
                <p class="pending-message">*承認待ちのため修正はできません。</p>
            @endif
        </div>
    </form>
</div>
@endsection

