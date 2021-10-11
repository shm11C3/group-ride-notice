@extends('layouts.layout')
@section('title','ダッシュボード')
@section('content')
<h3>登録情報</h3>
<div class="profile mt-5">
    <div class="row">
        <div class="col-8">
            <div class="row p-3 border-bottom">
                <span class="col-3 text-right profile-label">名前</span><span class="col-9 font-weight-bold">{{ $user[0]->name }}</span>
            </div>
            <div class="row p-3 border-bottom">
                <span class="col-3 text-right profile-label">メールアドレス</span><span class="col-9 font-weight-bold">{{ $user[0]->email }}</span>
            </div>
            <div class="row p-3 border-bottom">
                <span class="col-3 text-right profile-label">都道府県</span><span class="col-9 font-weight-bold">{{ $prefecture }}</span>
            </div>    
        </div>
        <div class="col-4">
            <div class="logout-btn">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-outline-primary mb-2">ログアウト</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection