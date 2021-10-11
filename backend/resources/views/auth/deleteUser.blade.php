@extends('layouts.layout')
@section('title','設定')
@section('content')
<h2>アカウントを削除</h2>
<div class="alert alert-danger" role="alert">
    <strong>注意 </strong>この操作はもとに戻せません！
  </div>
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
    <form class="update-password-form mt-5" method="POST" action="{{ route('deleteUser') }}">
        @csrf
        <div class="form-group">
            <label for="inputEmail1">パスワード</label>
            <input type="password" name="password" autocomplete="current-password" style="display:none;">
            <input v-model="password" type="password" name="current_password" class="form-control" placeholder="パスワード" autocomplete="off">
          </div>
          <div class="form-group mt-2">
            <label for="inputEmail1">"delete_me" と入力してください</label>
            <input v-model="inputDelete" type="text" class="form-control" aria-describedby="delete_me" placeholder="delete_me">
          </div>
          <div class="text-right">
            <button type="submit" class="btn btn-danger" v-bind:disabled="disableSubmitBtn">アカウントを削除する</button>
          </div>
    </form>
</div>
<script src="{{ mix('js/deleteUser.js') }}"></script>
@endsection