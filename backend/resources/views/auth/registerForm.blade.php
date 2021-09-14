@extends('layouts.layout')
@section('title','新規登録')
@section('content')
<div class="mt-10 mb-10">
    <h2>新規登録</h2>
</div>
@if ($errors->any())
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
<form class="login-form" method="POST" action="{{ route('register') }}">
    @csrf
    <div class="form-group">
        <label for="inputName">名前</label>
        <input type="text" name="name" class="form-control" placeholder="Name" value="{{ old('name') }}">
        <small id="nameInfo" class="form-text text-muted">公序良俗に反する内容は入力しないでください</small>
    </div>
    <div class="form-group">
      <label for="inputEmail1">メールアドレス</label>
      <input type="email" name="email" class="form-control" id="inputEmail1" aria-describedby="emailHelp" placeholder="Email" value="{{ old('email') }}">
      <small id="emailHelp" class="form-text text-muted">ここで入力したメールアドレスは公開されません</small>
    </div>
    <div class="form-group">
      <label for="inputPassword1">パスワード</label>
      <input type="password" name="password" class="form-control" id="inputPassword1" placeholder="Password" value="{{ old('password') }}">
    </div>
    <div class="form-group">
        <label for="inputPassword1">パスワード（確認）</label>
        <input type="password" class="form-control" id="inputPassword1" placeholder="Email (confirm)">
    </div>
    <div class="form-group">
      <label for="select1">お住まいの都道府県</label>
      <select name="prefecture_code" class="form-control" id="select1">
        <option value="1">北海道</option>
        <option value="2">青森県</option>
        <option value="3">岩手県</option>
        <option value="4">宮城県</option>
        <option value="5">秋田県</option>
        <option value="6">山形県</option>
        <option value="7">福島県</option>
        <option value="8">茨城県</option>
        <option value="9">栃木県</option>
        <option value="10">群馬県</option>
        <option value="11">埼玉県</option>
        <option value="12">千葉県</option>
        <option value="13">東京都</option>
        <option value="14">神奈川県</option>
        <option value="15">新潟県</option>
        <option value="16">富山県</option>
        <option value="17">石川県</option>
        <option value="18">福井県</option>
        <option value="19">山梨県</option>
        <option value="20">長野県</option>
        <option value="21">岐阜県</option>
        <option value="22">静岡県</option>
        <option value="23">愛知県</option>
        <option value="24">三重県</option>
        <option value="25">滋賀県</option>
        <option value="26">京都府</option>
        <option value="27">大阪府</option>
        <option value="28">兵庫県</option>
        <option value="29">奈良県</option>
        <option value="30">和歌山県</option>
        <option value="31">鳥取県</option>
        <option value="32">島根県</option>
        <option value="33">岡山県</option>
        <option value="34">広島県</option>
        <option value="35">山口県</option>
        <option value="36">徳島県</option>
        <option value="37">香川県</option>
        <option value="38">愛媛県</option>
        <option value="39">高知県</option>
        <option value="40">福岡県</option>
        <option value="41">佐賀県</option>
        <option value="42">長崎県</option>
        <option value="43">熊本県</option>
        <option value="44">大分県</option>
        <option value="45">宮崎県</option>
        <option value="46">鹿児島県</option>
        <option value="47">沖縄県</option>
      </select>
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
    <a class="btn btn-outline-primary" href="{{ route('showLogin') }}">登録済みの方はこちら</a>
</form>
@endsection