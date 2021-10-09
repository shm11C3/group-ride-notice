@extends('layouts.layout')
@section('title','設定')
@section('content')
<h2>パスワードを変更</h2>
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
<div v-if="">
    <form class="update-password-form mt-5" method="POST" action="{{ route('updatePassword') }}">
        @csrf
        <div class="form-group">
            <label for="inputEmail1">現在のパスワード</label>
            <input type="password" name="current_password" class="form-control" aria-describedby="currentPassword" placeholder="現在のパスワード" value="{{ old('currentPassword') }}">
          </div>
          <div class="form-group mt-2">
            <label for="inputEmail1">新しいパスワード</label>
            <input type="password" name="new_password" class="form-control" aria-describedby="new_password" placeholder="新しいパスワード" value="{{ old('new_password') }}" autocomplete="new-password">
          </div>
          <div class="form-group mt-2">
            <label for="inputEmail1">新しいパスワード（確認）</label>
            <input type="password" name="new_password_confirmation" class="form-control" aria-describedby="password_confirmationHelp" placeholder="新しいパスワード（確認）" value="{{ old('password_confirmation') }}">
          </div>
          <div class="text-right">
            <button type="submit" class="btn btn-success">送信</button>
          </div>
    </form>
</div>
@endsection