<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-cache">
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/style.css') }}" rel="stylesheet">
    <title>@yield('title') / Bipokele</title>
</head>
<body>
@include('layouts.header')
    <div class="container">
        @yield('content')
    </div>
<footer>
</footer>
<script src="{{ mix('js/app.js') }}"></script>
<script src="{{ asset('js/submit.js') }}"></script>
</body>
</html>