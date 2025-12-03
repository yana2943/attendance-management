@extends('layouts.common')

@section('css')
<link rel="stylesheet" href="{{ asset('css/application-list.css') }}">
@endsection

@section('content')
<div class="content-title">
    <h2>申請一覧</h2>
</div>

<div class="content">
    <div class="tab-container">
        <a href="{{ route('attendance_corrections.index', ['status' => 'pending']) }}"
            class="tab {{ $status === 'pending' ? 'active' : '' }}">承認待ち</a>
        <a href="{{ route('attendance_corrections.index', ['status' => 'approved']) }}"
            class="tab {{ $status === 'approved' ? 'active' : '' }}">承認済み</a>
    </div>
    <div class="border"></div>

    <table class="request-table">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($corrections as $correction)
                <tr>
                    <td>{{ $correction->status_label }}</td>
                    <td>{{ $correction->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($correction->work_date)->format('Y/m/d') }}</td>
                    <td>{{ $correction->note ?? '-' }}</td>
                    <td>{{ $correction->created_at->format('Y/m/d') }}</td>
                    <td>
                        @if($correction->attendance_id)
                            <a href="{{ route('attendance.detail', ['id' => $correction->attendance_id]) }}">
                                詳細
                            </a>
                        @else
                            <a href="{{ route('attendance.detail', ['id' => \Carbon\Carbon::parse($correction->work_date)->format('Y-m-d'), 'user_id' => $correction->user_id]) }}">
                                詳細
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection