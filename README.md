# Bipokele

## 概要

Bipokele は自転車乗りが走行会・練習会を主催、または走行会・練習会に参加することができるWEBアプリケーションです。

### URL
https://bipokele.com

### ホーム画面

https://user-images.githubusercontent.com/78523393/145537613-6b27224b-b65b-48d2-bb37-980683b1c3b3.mp4


## 使用技術

- サーバーサイド
  - PHP 8 (Laravel 8, Laravel-admin, socialite) 
 
- クライアントサイド
  - HTML(Blade)
  - JavaScript (Vue.js 2.6, JQuery)
  - SCSS (Bootstrap-honoka 4.4)

- インフラ
  - Nginx (Docker 20.10 / Docker-Compose 2.2)
  - MySQL (ローカル：Docker 20.10 / Docker-Compose 2.2/本番：AWS RDS)
  - PHPMyAdmin(ローカル環境時)
  - AWS 以下インフラ構成図参照

![bipokele drawio](https://user-images.githubusercontent.com/78523393/137576629-fca9fe70-3924-4df0-a96f-7e64d063bfe6.png)


- 開発環境・その他使用ツール
  - OS:        Ubuntu 18.04 (WSL2)
  - エディタ:   Visual Studio Code
  - draw.io
  - Postman
  - 画像加工等: Adobe Illustrator / Photoshop, RCKP, 縮小革命
