@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-approve.css') }}">

@endsection

@section('content')
<div class="content-title">
    <h2>勤怠承認</h2>
</div>

@php use Carbon\Carbon; @endphp

<div class="attendance-detail">
    <div class="detail-item">
        <span class="label">名前</span>
        <span class="value">{{ $attendance->user->name ?? '' }}</span>
    </div>
    <div class="detail-item">
        <span class="label">日付</span>
        <span class="value">{{ Carbon::parse($attendance->work_date)->format('Y年n月j日') }}</span>
    </div>
    <div class="detail-item">
        <span class="label">出勤・退勤</span>
        <span class="value">
            {{ $attendance->start_time && $attendance->end_time
                ? Carbon::parse($attendance->start_time)->format('H:i') . ' ～ ' . Carbon::parse($attendance->end_time)->format('H:i')
                : '' }}
        </span>
    </div>
    <div class="detail-item">
        <span class="label">休憩1</span>
        <span class="value">
            {{ $attendance->break_start && $attendance->break_end
                ? Carbon::parse($attendance->break_start)->format('H:i') . ' ～ ' . Carbon::parse($attendance->break_end)->format('H:i')
                : '' }}
        </span>
    </div>
    <div class="detail-item">
        <span class="label">休憩2</span>
        <span class="value">
            {{ $attendance->break2_start && $attendance->break2_end
                ? Carbon::parse($attendance->break2_start)->format('H:i') . ' ～ ' . Carbon::parse($attendance->break2_end)->format('H:i')
                : '' }}
        </span>
    </div>
    <div class="detail-item">
        <span class="label">備考</span>
        <span class="value">{{ $attendance->note ?? '-' }}</span>
    </div>
    <div class="detail-actions">
    <button id="approveBtn"
        class="approve-button"
        data-url="{{ route('admin.correction.approve', ['id' => $attendance->id]) }}"
        {{ $attendance->approval_status === '承認済' ? 'disabled' : '' }}>
        {{ $attendance->approval_status === '承認済' ? '承認済' : '承認' }}
    </button>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('approveBtn');

    btn?.addEventListener('click', () => {
        if (btn.disabled) return;

        const url = "{{ route('admin.correction.approve', ['id' => $attendance->id]) }}";

        fetch(url, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "application/json",
            },
        })
        .then(res => {
            if (res.status !== 202) throw new Error("承認失敗");
            return res.json();
        })
        .then(data => {
            if (data.success) {
                btn.textContent = "承認済";
                btn.disabled = true;
            }
        })
        .catch(() => alert("承認に失敗しました"));
    });
});
</script>
@endsection