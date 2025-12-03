<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coachtech Attendance Management</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
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
        <div class="login-form__content">
            <div class="login-form__title">
                <h2>ログイン</h2>
            </div>
            <form class="form" action="{{ route('login') }}" method="post">
                @csrf
                <div class="form-body">
                    <div class="form__group">
                        <div class="from__group-title">
                            <label class="form__label--item">メールアドレス</label>
                        </div>
                        <div class="form__group-content">
                            <div class="form__input--text">
                                <input type="email" name="email" value="{{ old('email') }}" />
                            </div>
                        </div>
                        <div class="form__error">
                            @error('email')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                    <div class="form__group">
                        <div class="from__group-title">
                            <label class="form__label--item">パスワード</label>
                        </div>
                        <div class="form__group-content">
                            <div class="form__input--text">
                                <input type="password" name="password" />
                            </div>
                        </div>
                        <div class="form__error">
                            @error('password')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                    <div class="form__button">
                        <button class="form__button-submit" type="submit">ログインする</button>
                    </div>
                    <div class="form__link">
                        <a href="/register">会員登録はこちら</a>
                    </div>
                </div>
            </form>
        </div>
    </main>
</body>

</html>