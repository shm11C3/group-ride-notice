@extends('layouts.layout')
@section('title','新規登録')
@section('content')
<div class="content">
    <div class="border rounded user-form">
        <h2>新規登録</h2>
        <form class="login-form" method="POST" action="{{ route('register') }}">
        @csrf
        <!--<div class="alert alert-danger">
            
        </div>-->
        <input type="text" class="login-input form-control" name="name" placeholder="名前" required autofocus value="{{ old('name') }}">
        <input type="email" class="login-input form-control" name="email" placeholder="メールアドレス" required value="{{ old('email') }}">
        <input type="password" class="login-input form-control" name="password" placeholder="パスワード" required value="{{ old('password') }}">
        <input name="prefecture_code" value="2">
        <input type="password" class="login-input form-control" name="password_confirmation" placeholder="パスワード(確認)" required>
        <button class="btn btn-primary" type="submit">登録</button>
        <a class="btn btn-outline-primary" href="">登録済みの方はこちら</a>
        </form>
    </div>
</div>
@endsection