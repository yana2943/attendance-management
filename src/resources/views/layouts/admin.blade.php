<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coachtech Attendance Management</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout-admin.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__logo">
                <a href="{{ route('attendance.index') }}">
                    <img src="{{ asset('images/logo.svg') }}" alt="ロゴ">
                </a>
            </div>
            <nav>
                <ul class="header-nav">
                    <li><a href="{{ route('admin.attendance.list') }}" class="admin-attendance-link">勤怠一覧</a></li>
                    <li><a href="{{ route('admin.staff.list') }}" class="admin-staff-link">スタッフ一覧</a></li>
                    <li><a href="{{ route('admin.correction.list') }}" class="admin-approve-link">申請一覧</a></li>
                    <li>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="logout-link">ログアウト</button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
        @yield('scripts')
    </main>
</body>
</html>