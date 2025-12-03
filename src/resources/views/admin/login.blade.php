<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coachtech Attendance Management</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-login.css') }}">
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__logo">
                <img src="{{ asset('images/logo.svg') }}" alt="ロゴ">
            </div>
        </div>
    </header>

    <main>
        <div class="login-form__content">
            <div class="login-form__title">
                <h2>管理者ログイン</h2>
            </div>

            <form class="form" action="{{ route('admin.login.submit') }}" method="POST">
            @csrf
            <input type="hidden" name="is_admin_login" value="1">
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
                            @if ($errors->has('email'))
                                {{ $errors->first('email') }}
                            @endif
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
                            <div class="form__error">
                                @if ($errors->has('password'))
                                    {{ $errors->first('password') }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form__button">
                        <button class="form__button-submit" type="submit">管理者ログインする</button>
                    </div>
                </div>
            </form>
        </div>
    </main>
</body>