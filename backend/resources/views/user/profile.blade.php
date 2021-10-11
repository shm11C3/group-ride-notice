@extends('layouts.layout')
@section('title','ユーザー')
@section('content')
<div class="media ride shadow m-5">
    <svg class="bd-placeholder-img align-self-start profile-img" width="64" height="64" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Placeholder: 64x64"><title>Placeholder</title><rect width="100%" height="100%" fill="#868e96"/><text x="50%" y="50%" fill="#dee2e6" ></text></svg>
    <div class="media-body m-2">
        <div class="btn-toolbar">
            <div class="username-title">
                <p>
                    <span class="font-weight-bold">{{ '@'.$user->name }}</span><br>
                    {{ $prefecture }}
                </p>
            </div>
            @if(Auth::user()->uuid === $user->uuid)
            <div class="btn-group ml-auto mb-5">
                <a type="button" class="btn btn-outline-secondary" href="{{ route('showConfig') }}">プロフィールを編集</a>
            </div>
            @endif
        </div>
        <div class="user-intro border-top">
            <span class="text-muted additional-txt">自己紹介</span><br>
            @if(strlen($user->user_intro) > 0)
            <p class="mt-2">{{ html($user->user_intro) }}</p>
            @endif
        </div>
        <div class="user-url-group border-top">
            <div class="user-url mt-2">
                <span class="text-muted additional-txt">{{ $user->name }}さんのホームページ</span><br>
                <a style="color: rgb(0, 174, 255)" href="{{ $user->user_url }}" target="_blank" rel="noopener noreferrer">{{ $user->user_url }}</a>
            </div>
            <div class="user-url mt-2">
                <span class="text-muted additional-txt">Facebookアカウント</span><br>
                @if(strlen($user->fb_username) > 0)
                <a style="color: rgb(0, 174, 255)" href="https://Facebook.com/{{ $user->fb_username }}" target="_blank" rel="noopener noreferrer">{{ $user->fb_username }}</a>
                @endif    
            </div>
            <div class="user-url mt-2">
                <span class="text-muted additional-txt">Twitterアカウント</span><br>
                @if(strlen($user->tw_username) > 0)
                <a style="color: rgb(0, 174, 255)" href="https://twitter.com/{{ $user->tw_username }}" target="_blank" rel="noopener noreferrer">{{ '@'.$user->tw_username }}</a>
                @endif
            </div>
            <div class="user-url mt-2">
                
                <span class="text-muted additional-txt">Instagramアカウント</span><br>
                @if(strlen($user->ig_username) > 0)
                <a style="color: rgb(0, 174, 255)" href="https://www.instagram.com/{{ $user->ig_username }}" target="_blank" rel="noopener noreferrer">{{ $user->ig_username }}</a>
                @endif
            </div>
        </div>
    </div>
</div>
<div></div>
@endsection