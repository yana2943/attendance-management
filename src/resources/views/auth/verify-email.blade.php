<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coachtech Attendance Management</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/verify.css') }}">
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__logo">
                <a href="{{ route('attendance.index') }}">
                    <img src="{{ asset('images/logo.svg') }}" alt="ロゴ">
                </a>
            </div>
        </div>
    </header>

    <main>
        <div class="content">
            <div class="content-sentence">
                <p>登録していただいたメールアドレスに認証メールを送 付しました。</p>
                <p>メール認証を完了してください。</p>
            </div>
            <div class="form__button">
                <a href="http://localhost:8026" target="_blank" class="mailhog-button">
                    認証はこちらから
                </a>
            </div>

            <p>
                <a href="{{ route('verification.send') }}"onclick="event.preventDefault(); document.getElementById('resend-form').submit();"class="resend-link">
                確認メールを再送する
                </a>
            </p>

            <form id="resend-form" method="POST" action="{{ route('verification.send') }}" style="display: none;">
                @csrf
            </form>
        </div>
    </main>
</body>
</html>