<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coachtech Attendance Management</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
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
                    <li><a href="/attendance" class="attendance-link">勤怠</a></li>
                    <li><a href="/attendance/list" class="attendance-list-link">勤怠一覧</a></li>
                    <li><a href="{{ route('attendance_corrections.index') }}" class="stamp-correction-link">申請</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="post">
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