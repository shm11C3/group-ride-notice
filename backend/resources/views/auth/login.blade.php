




@extends('layouts.layout')
@section('title','ログイン')
@section('content')
<div class="content">
    <div class="border rounded user-form">
        <h2>ログイン</h2>
        <form class="login-form" method="POST" action="{{ route('login') }}">
        @csrf
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <x-alert type="error" :session="session('login_error')"/>
                <x-alert type="error" :session="session('account_lock')"/>
                <x-alert type="danger" :session="session('logout_success')"/>
            </div>
            @endif
            <div class="login-form form-group">
                <label for="inputEmail">メールアドレス</label>
                <input type="email" class="login-input form-control" name="email" placeholder="メールアドレス" required autofocus value="{{ old('email') }}">
            </div>
            <div class="login-form form-group">
                <label for="inputPassword">パスワード</label>
                <input type="password" class="login-input form-control" name="password" placeholder="パスワード" required>
            </div>
            <input type="hidden" name="remember" value="0">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" value="1" checked>
                <label class="form-check-label" for="inputRemember">アカウントを記憶</label>
            </div>
            <button class="btn btn-primary" type="submit">ログイン</button>
            <a class="btn btn-outline-primary" href="{{ route('showRegister') }}">新規登録はこちら</a>
        </form>
    </div>
</div>
@endsection