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
<div id="app" v-cloak>
  <form class="login-form" method="POST" action="{{ route('login') }}">
    @csrf
      <div class="form-group">
        <label for="inputEmail1">メールアドレス</label>
        <input v-model="email" type="email" name="email" class="form-control" id="inputEmail1" aria-describedby="emailHelp" placeholder="Email" value="{{ old('email') }}" autocomplete="email">
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
      <div class="text-right">
        <a class="btn btn-secondary" href="{{ route('showRegister') }}">新規登録はこちら</a>
          <button v-bind:disabled="!submitStatus" type="submit" class="btn btn-success">送信</button>
      </div>
      <div class="oauth-btn-group">
        <p class="m-3 font-weight-bold">連携アカウントでログイン</p>
        <a class="mt-3 mb-3 pt-3 pb-3 btn-block btn-social btn btn-outline-google" href="{{ route('googleAuth') }}">
          <img class="mr-2" src="{{ asset('img/google_icon.png') }}" alt="google_login">
          <span class="btn-google-txt">Googleでログイン</span>
        </a>
        <a class="mt-3 mb-3 pt-3 pb-3 btn-block btn btn-strava strava-auth-btn" href="{{ route('stravaAuth') }}">
          <i class="fab fa-strava strava-icon mr-2"></i>
          <span class="btn-strava-txt">STRAVAでログイン</span>
        </a>
      </div>
  </form>
</div>
<script src="{{ mix('js/loginForm.js') }}"></script>
@endsection
