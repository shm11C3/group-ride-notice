@extends('layouts.layout')
@section('title','ログイン')
@section('content')
<h2>ログイン</h2>
@if ($errors->any())
  @foreach ($errors->all() as $error)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>{{ $error }}</strong>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endforeach
<x-alert type="danger" :session="session('login_error')"/>
<x-alert type="danger" :session="session('account_lock')"/>
<x-alert type="danger" :session="session('logout_success')"/>
@endif
<div id="app">
  <form class="login-form" method="POST" action="{{ route('login') }}">
    @csrf
      <div class="form-group">
        <label for="inputEmail1">メールアドレス</label>
        <input v-model="email" type="email" name="email" class="form-control" id="inputEmail1" aria-describedby="emailHelp" placeholder="Email" value="{{ old('email') }}">
      </div>
      @if ($errors->has('isInvalidPassword'))
        <div class="form-group has-danger">
          <label class="form-control-label" for="inputDanger1">パスワード</label>
          <input v-model="password" type="password" name="password" class="form-control is-invalid" id="inputPassword1" placeholder="Password" value="{{ old('password') }}">
          <div class="invalid-feedback">パスワードが違います</div>
        </div>
      @else
        <div class="form-group">
          <label for="inputPassword1">パスワード</label>
          <input v-model="password" type="password" name="password" class="form-control" id="inputPassword1" placeholder="Password" value="{{ old('password') }}">
        </div>
      @endif
      <fieldset class="form-group">
        <div class="form-check">
          <label class="form-check-label">
            <input name="remember" class="form-check-input" type="checkbox" value="1" checked="">
            アカウントを記憶
          </label>
        </div>
      </fieldset>
      <div>
          <button v-bind:disabled="!submitStatus" type="submit" class="btn btn-primary">送信</button>
          <a class="btn btn-link" href="{{ route('showRegister') }}">新規登録はこちら</a>
      </div>
  </form>
</div>
<script src="{{ mix('js/loginForm.js') }}"></script>
@endsection