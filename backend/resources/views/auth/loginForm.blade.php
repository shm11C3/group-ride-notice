@extends('layouts.layout')
@section('title','ログイン')
@section('content')
<div id="app">
  <h2>ログイン</h2>
  <form class="login-form" method="POST" action="{{ route('login') }}">
  @csrf
      <div class="form-group">
        <label for="inputEmail1">メールアドレス</label>
        <input v-model="email" type="email" name="email" class="form-control" id="inputEmail1" aria-describedby="emailHelp" placeholder="Email" value="{{ old('email') }}">
      </div>
      <div class="form-group">
        <label for="inputPassword1">パスワード</label>
        <input v-model="password" type="password" name="password" class="form-control" id="inputPassword1" placeholder="Password" value="{{ old('password') }}">
      </div>
      <fieldset class="form-group">
        <div class="form-check">
          <label class="form-check-label">
            <input name="remember" class="form-check-input" type="checkbox" value="1" checked="">
            アカウントを記憶
          </label>
        </div>
      </fieldset>
      <div>
        <!--<div v-if="submitStatus">-->
          <button  v-bind:disabled="!submitStatus" type="submit" class="btn btn-primary">送信</button>
          <a class="btn btn-outline-primary">新規登録はこちら</a>
        <!--</div>
        <div v-else>
          <button type="button" class="btn btn-primary" disabled>送信</button>
          <a class="btn btn-outline-primary" href="{{ route('showRegister') }}">新規登録はこちら</a>
        </div>-->
      </div>
  </form>
</div>
<script src="{{ mix('js/loginForm.js') }}"></script>
@endsection