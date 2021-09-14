@extends('layouts.layout')
@section('title','ログイン')
@section('content')
<h2>ログイン</h2>
<form class="login-form" method="POST" action="{{ route('login') }}">
@csrf
    <div class="form-group">
      <label for="inputEmail1">メールアドレス</label>
      <input type="email" name="email" class="form-control" id="inputEmail1" aria-describedby="emailHelp" placeholder="Email" value="{{ old('email') }}">
    </div>
    <div class="form-group">
      <label for="inputPassword1">パスワード</label>
      <input type="password" name="password" class="form-control" id="inputPassword1" placeholder="Password" value="{{ old('password') }}">
    </div>
    <fieldset class="form-group">
      <div class="form-check">
        <label class="form-check-label">
          <input name="remember" class="form-check-input" type="checkbox" value="1" checked="">
          アカウントを記憶
        </label>
      </div>
    </fieldset>
    <button type="submit" class="btn btn-primary">送信</button>
    <a class="btn btn-outline-primary" href="{{ route('showRegister') }}">新規登録はこちら</a>
</form>
@endsection