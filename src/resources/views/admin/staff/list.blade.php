@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/staff-list.css') }}">
@endsection

@section('content')
<div class="content-title">
    <h2>スタッフ一覧</h2>
</div>

<div class="staff-list">
    <table class="staff-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>月次勤怠</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($staffs as $staff)
                <tr>
                    <td>{{ $staff->name }}</td>
                    <td>{{ $staff->email }}</td>
                    <td>
                        <a href="{{ route('admin.attendance.staff', ['id' => $staff->id]) }}" class="detail-link">
                            詳細
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection