@extends('layouts.layout')
@section('title','ダッシュボード')
@section('content')
<div class="profile">
    <h3>プロフィール</h3>
    <div class="row">
        <div class="col-8">
            <ul>
                <li>名前 : {{ $user[0]->name }}</li>
                <li>メールアドレス : {{ $user[0]->email }}</li>
                <li>都道府県: {{ $user[0]->prefecture_code }}</li>
            </ul>
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