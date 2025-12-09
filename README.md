# attendance-management

## 環境構築
Dockerビルド
1. docker-compose.ymlを作成
2. docker conpose up -d --build

Laravel環境構築
1. docker-compose exec php bash
2. composer インストール
3. .envの変更
4. php artisan key:generate
5. php artisan migtate
6. php artisan db seed

## 使用技術
・Laravel Framework 8.83.29  
・PHP 8.1.33  
・nginx 1.21.1  
・mysql 8.0.26  
・phpmyadmin 5.2.
・mailhog

## メール認証
mailhogというツールを使用しています。<br>
以下のリンクからインストールをしてください。　<br>
https://mailtrap.io/](https://github.com/mailhog/MailHog/releases

.envの設定は以下の通りです。
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME="${APP_NAME}"


## URL
・開発環境:localhost/  
・管理者画面:localhost/admin/  
・phpadmin:localhost:8080/

##　テストユーザー

・テスト用のユーザー以下の通りです。

名前：テスト　１
メールアドレス：123@456.com
パスワード：password

名前：テスト　２
メールアドレス：456@123.com
パスワード：password

名前：管理者
メールアドレス：kanri@aaa.com
パスワード：kanripass
