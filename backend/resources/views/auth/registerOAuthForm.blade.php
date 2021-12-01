@extends('layouts.layout')
@section('title','新規登録')
@section('content')
<div class="mt-10 mb-10">
    <h2>新規登録</h2>
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
@endif
<div id="app" v-cloak>
  <form class="login-form" method="POST" action="{{ route('registerOAuthUser') }}">
    @csrf
    <div class="form-group">
      <img class="d-block mx-auto" alt="userProfileImg" v-bind:src="user_profile_img_path">
    </div>
    <div class="form-group">
      <label for="inputName">名前</label>
      <input v-model="name" type="text" name="name" v-bind:class="'form-control '+nameClass" placeholder="Name" value="{{ old('name') }}" autocomplete="name">
      <small id="nameInfo" class="form-text text-muted">公序良俗に反する内容は入力しないでください</small>
    </div>
    <div class="form-group">
      <label for="select1">お住まいの都道府県</label>
      <select name="prefecture_code" class="form-control" id="select1">
        <option value="0">選択してください</option>
        <option v-for="(prefecture, i) in prefecture_arr" v-bind:value="i+1">@{{ prefecture }}</option>
      </select>
      <div class="text-right">
        <button v-bind:disabled="!submitStatus" type="submit" class="btn btn-success">保存</button>
      </div>
    </div>
  </form>
</div>
<script src="{{ mix('js/registerOAuthForm.js') }}"></script>
@endsection
