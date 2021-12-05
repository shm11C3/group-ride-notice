@extends('layouts.layout')
@section('title','登録エラー')
@section('content')
<div class="alert alert-danger" role="alert">
    このアカウントはすでに登録されています。
</div>
<a class="btn btn-outline-dark" href="{{ route('showDashboard')}}">ダッシュボードに戻る</a>
@endsection
