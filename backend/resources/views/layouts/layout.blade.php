<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-cache">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
    <title>アプリ名/@yield('title')</title>
</head>
<body>
    @guest
    <span id="hide-right-post-btn"></span>
    @endguest
    <header class="fixed-top">
       
    </header>
    <div class="container">
        @yield('content')
    </div>
<footer>
</footer>
</body>
</html>