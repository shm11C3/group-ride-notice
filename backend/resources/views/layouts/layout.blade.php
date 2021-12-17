<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-cache">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/3553d1e2a8.js" crossorigin="anonymous"></script>
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
    <title>@yield('title') / Bipokele</title>
</head>
<body>
@include('layouts.header')
    <div class="container">
        @yield('content')
    </div>
<footer>
    <div class="footer">
    </div>
</footer>
<script src="{{ mix('js/app.js') }}"></script>
<!--<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>-->
<script src="{{ mix('js/submit.js') }}"></script>
</body>
</html>
