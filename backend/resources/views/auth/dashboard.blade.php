@extends('layouts.layout')
@section('title','ダッシュボード')
@section('content')
<h3>登録情報</h3>
<div class="profile mt-5">
    <div class="row">
        <div class="col-sm-8">
            <div class="row p-3 border-bottom">
                <span class="col-lg-3 text-lg-right profile-label">名前</span><span class="col-lg-9 font-weight-bold">{{ $user[0]->name }}</span>
            </div>
            <div class="row p-3 border-bottom">
                <span class="col-lg-3 text-lg-right profile-label">
                    メールアドレス
                </span>
                <span class="col-lg-9 font-weight-bold">
                    @if($user[0]->email){{ $user[0]->email }} @else 登録無し@endif
                </span>
            </div>
            <div class="row p-3 border-bottom">
                <span class="col-lg-3 text-lg-right profile-label">
                    連携アカウント
                </span>
                <span class="col-lg-9 font-weight-bold">
                    @if($user[0]->google_user)<img src="{{ asset('img/google_icon.png') }}">@endif
                </span>
            </div>
            <div class="row p-3 border-bottom">
                <span class="col-lg-3 text-lg-right profile-label">都道府県</span><span class="col-lg-9 font-weight-bold">{{ $prefecture }}</span>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="dashboard-btn">
                <a class="btn btn-outline-primary m-2" href="{{ route('showConfig') }}">登録情報を変更</a>
            </div>
            <div class="logout-btn">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-outline-primary m-2">ログアウト</button>
                </form>
            </div>
            <div class="dashboard-btn">
                <a class="btn btn-outline-primary m-2" href="{{ route('googleAuth') }}">連携アカウントを追加</a>
            </div>
        </div>
    </div>
</div>
@endsection
