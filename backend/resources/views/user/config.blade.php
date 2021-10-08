@extends('layouts.layout')
@section('title','ダッシュボード')
@section('content')
<h3>プロフィール</h3>
<div id="app" class="profile mt-5">
    <div v-if="httpErrors">
        <div v-for="(httpError, index) in httpErrors">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                @{{ httpError }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
    <div v-if="profile_isLoad">
        <div class="d-flex justify-content-center">
            <div class="spinner-grow text-success mb-30" style="width: 3rem; height: 3rem; margin-top: 200px;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
<!--<div v-if="profile&&user">-->
    <div v-if="profile">
        <div class="row">
            <div class="col-2">
                <div class="list-group">
                    <button v-on:click="changePage(0)" v-bind:class="'list-group-item list-group-item-action '+listBtnStatus[0]">プロフィール</button>
                    <button v-on:click="changePage(1)" v-bind:class="'list-group-item list-group-item-action '+listBtnStatus[1]">登録情報</button>
                  </div>
            </div>
            <div class="col-10" v-if="pageStatus==0">
                <div class="row p-3 border-bottom">
                    <span class="col-4 text-right profile-label">名前</span>
                    <div class="col-8">
                        <div v-if="name_formStatus">
                            <input class="form-control" v-model="profile.name">
                        </div>
                        <div v-else>
                            <span class="font-weight-bold">@{{ profile.name }} <button class="hidden-btn" v-on:click="name_openUpdate"><i class="fas fa-edit edit-btn"></i></button></span>
                        </div>
                    </div>                
                </div>

                <div class="row p-3 border-bottom">
                    <span class="col-4 text-right profile-label">都道府県</span>
                    <div class="col-8">
                        <div v-if="prefecture_formStatus">
                            
                        </div>
                        <div v-else>
                            <span class="font-weight-bold">@{{ prefecture[profile.prefecture_code-1] }} <button class="hidden-btn" v-on:click="prefecture_openUpdate"><i class="fas fa-edit edit-btn"></i></button></span>
                        </div>
                    </div>                
                </div>
                <div class="row p-3 border-bottom">
                    <span class="col-4 text-right profile-label">ホームページ</span>
                    <div class="col-8">
                        <div v-if="url_formStatus">
                            <input class="form-control" v-model="profile.user_url">
                        </div>
                        <div v-else>
                            <span class="font-weight-bold">@{{ profile.user_url }} <button class="hidden-btn" v-on:click="url_openUpdate"><i class="fas fa-edit edit-btn"></i></button></span>
                        </div>
                    </div>                
                </div>
                <div class="row p-3 border-bottom">
                    <span class="col-4 text-right profile-label">フェイスブックユーザ名</span>
                    <div class="col-8">
                        <div v-if="fb_username_formStatus">
                            <input class="form-control" v-model="profile.fb_username">
                        </div>
                        <div v-else>
                            <span class="font-weight-bold">@{{ profile.fb_username }} <button class="hidden-btn" v-on:click="fb_username_openUpdate"><i class="fas fa-edit edit-btn"></i></button></span>
                        </div>
                    </div>                
                </div>

                <div class="row p-3 border-bottom">
                    <span class="col-4 text-right profile-label">ツイッターユーザ名</span>
                    <div class="col-8">
                        <div v-if="tw_username_formStatus">
                            <input class="form-control" v-model="profile.tw_username">
                        </div>
                        <div v-else>
                            <span class="font-weight-bold">@{{ profile.tw_username }} <button class="hidden-btn" v-on:click="tw_username_openUpdate"><i class="fas fa-edit edit-btn"></i></button></span>
                        </div>
                    </div>                
                </div>

                <div class="row p-3 border-bottom">
                    <span class="col-4 text-right profile-label">インスタグラムユーザ名</span>
                    <div class="col-8">
                        <div v-if="ig_username_formStatus">
                            <input class="form-control" v-model="profile.ig_username">
                        </div>
                        <div v-else>
                            <span class="font-weight-bold">@{{ profile.ig_username }} <button class="hidden-btn" v-on:click="ig_username_openUpdate"><i class="fas fa-edit edit-btn"></i></button></span>
                        </div>
                    </div>                
                </div>
                <div class="row p-3 border-bottom">
                    <span class="col-4 text-right profile-label">自己紹介</span>
                    <div class="col-8">
                        <div v-if="user_intro_formStatus">
                            
                            
                        </div>
                        <div v-else>
                            
                            <span class="col-8 font-weight-bold">@{{ profile.user_intro }}</span>
                        </div>
                        <button class="hidden-btn" v-on:click="user_intro_openUpdate"><i class="fas fa-edit edit-btn"></i></button>
                    </div>
                </div>

                <button type="submit" class="btn btn-success" v-on:click="profile_update">保存する</button>

            </div>
            <div class="col-8" v-if="pageStatus==1">
                <div class="row p-3 border-bottom">
                    <span class="col-3 text-right profile-label">メールアドレス</span><span class="col-9 font-weight-bold">あ</span>
                </div>
                <div class="row p-3 border-bottom">
                    <span class="col-3 text-right profile-label">パスワードを変更</span><span class="col-9 font-weight-bold">あ</span>
                </div>
                <div class="row p-3 border-bottom">
                    <span class="col-3 text-right profile-label"></span><span class="col-9 font-weight-bold">あ</span>
                </div>
                <div class="logout-btn">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-outline-primary mb-2">ログアウト</button>
                    </form>
                </div>
            </div>
            <div class="col-8" v-if="pageStatus==2">
                <div class="row p-3 border-bottom">
                    <span class="col-3 text-right profile-label">名前</span><span class="col-9 font-weight-bold">ユーザーネーム</span>
                </div>
                <div class="row p-3 border-bottom">
                    <span class="col-3 text-right profile-label">メールアドレス</span><span class="col-9 font-weight-bold">Email</span>
                </div>
                <div class="row p-3 border-bottom">
                    <span class="col-3 text-right profile-label">都道府県</span><span class="col-9 font-weight-bold">都道府県</span>
                </div>    
            </div>
        </div>
    </div>
    <div v-else>
        <div v-if="!profile_isLoad">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                無効なIDです
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
</div>
<script src="{{ mix('js/userConfig.js') }}"></script>
@endsection