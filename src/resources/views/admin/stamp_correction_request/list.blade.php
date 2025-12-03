@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-application-list.css') }}">
@endsection

@section('content')
<div class="content-title">
    <h2>申請一覧</h2>
</div>

<div class="content">
    <div class="tab-container">
        <a href="{{ route('admin.correction.list', ['status' => 'pending']) }}" class="tab {{ $status === 'pending' ? 'active' : '' }}">
            承認待ち
        </a>
        <a href="{{ route('admin.correction.list', ['status' => 'approved']) }}" class="tab {{ $status === 'approved' ? 'active' : '' }}">
            承認済み
        </a>
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
                @php
                    $date = \Carbon\Carbon::parse($correction->work_date);
                @endphp
                <tr>
                    <td>{{ $correction->status_label }}</td>
                    <td>{{ $correction->user->name }}</td>
                    <td>{{ $date->format('Y/m/d') }}</td>
                    <td>{{ $correction->note ?? '---' }}</td>
                    <td>{{ $correction->created_at->format('Y/m/d H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.correction.approve.show', ['id' => $correction->id]) }}">
                            詳細
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection