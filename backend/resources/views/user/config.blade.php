@extends('layouts.layout')
@section('title','設定')
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
    <div v-if="update">
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>更新しました！</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
    </div>
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
                    <span class="col-4 text-right profile-label">名前 <button class="hidden-btn" v-on:click="name_openUpdate"><i class="fas fa-edit edit-btn"></i></button></span>
                    <div class="col-8">
                        <div v-if="name_formStatus">
                            <input class="form-control" v-model="profile.name">
                        </div>
                        <div v-else>
                            <span class="font-weight-bold">@{{ profile.name }}</span>
                        </div>
                    </div>                
                </div>

                <div class="row p-3 border-bottom">
                    <span class="col-4 text-right profile-label">都道府県 <button class="hidden-btn" v-on:click="prefecture_openUpdate"><i class="fas fa-edit edit-btn"></i></button></span>
                    <div class="col-8">
                        <div v-if="prefecture_formStatus">
                            <select class="form-control" v-model="profile.prefecture_code">
                                <option v-for="(prefectureElement, index) in prefecture" v-bind:value="index+1">
                                    @{{ prefectureElement }}
                                </option>
                            </select>
                        </div>
                        <div v-else>
                            <span class="font-weight-bold">@{{ prefecture[profile.prefecture_code-1] }}</span>
                        </div>
                    </div>                
                </div>
                <div class="row p-3 border-bottom">
                    <span class="col-4 text-right profile-label">ホームページ <button class="hidden-btn" v-on:click="url_openUpdate"><i class="fas fa-edit edit-btn"></i></button></span>
                    <div class="col-8">
                        <div v-if="url_formStatus">
                            <input class="form-control" v-model="profile.user_url">
                        </div>
                        <div v-else>
                            <span class="font-weight-bold">@{{ profile.user_url }}</span>
                        </div>
                    </div>                
                </div>
                <div class="row p-3 border-bottom">
                    <span class="col-4 text-right profile-label">Facebookユーザ名 <button class="hidden-btn" v-on:click="fb_username_openUpdate"><i class="fas fa-edit edit-btn"></i></button></span>
                    <div class="col-8">
                        <div v-if="fb_username_formStatus">
                            <input class="form-control" v-model="profile.fb_username">
                        </div>
                        <div v-else>
                            <span class="font-weight-bold">@{{ profile.fb_username }}</span>
                        </div>
                    </div>                
                </div>

                <div class="row p-3 border-bottom">
                    <span class="col-4 text-right profile-label">Twitterユーザ名 <button class="hidden-btn" v-on:click="tw_username_openUpdate"><i class="fas fa-edit edit-btn"></i></button></span>
                    <div class="col-8">
                        <div v-if="tw_username_formStatus">
                            <input class="form-control" v-model="profile.tw_username">
                        </div>
                        <div v-else>
                            <span class="font-weight-bold">@{{ profile.tw_username }}</span>
                        </div>
                    </div>                
                </div>

                <div class="row p-3 border-bottom">
                    <span class="col-4 text-right profile-label">Instagramユーザ名 <button class="hidden-btn" v-on:click="ig_username_openUpdate"><i class="fas fa-edit edit-btn"></i></button></span>
                    <div class="col-8">
                        <div v-if="ig_username_formStatus">
                            <input class="form-control" v-model="profile.ig_username">
                        </div>
                        <div v-else>
                            <span class="font-weight-bold">@{{ profile.ig_username }}</span>
                        </div>
                    </div>                
                </div>
                <div class="row p-3 border-bottom">
                    <span class="col-4 text-right profile-label">自己紹介 <button class="hidden-btn" v-on:click="user_intro_openUpdate"><i class="fas fa-edit edit-btn"></i></button></span>
                    <div class="col-8">
                        <div v-if="user_intro_formStatus">
                            <textarea class="form-control" v-model="profile.user_intro" rows="3"></textarea>
                        </div>
                        <div v-else>
                            <p class="col-8 font-weight-bold"><span v-for="(replacedUser_intro) in replacedUser_introArr">@{{ replacedUser_intro }}<br></span></p>
                        </div>
                    </div>
                </div>
                <div class="text-right mt-2 mr-5 mb-5 ml-auto">
                    <div v-if="isPush">
                        <button type="submit" class="btn btn-success" disabled>
                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                            送信中...
                        </button>
                    </div>
                    <div v-else>
                        <button type="submit" class="btn btn-success" v-on:click="profile_update">保存する</button>
                    </div>
                </div>
            </div>
            <div class="col-8" v-if="pageStatus==1">
                <div class="row p-3 border-bottom">
                    <span class="col-4 text-right profile-label">登録日</span>
                    <span class="col-8 font-weight-bold">@{{ created_at[0] }} @{{ created_at[1] }}</span>
                </div>
                <div class="row p-3 border-bottom">
                    <span class="col-4 text-right profile-label">メールアドレス</span>
                    <span class="col-8 font-weight-bold">@{{ profile.email }}</span>
                </div>
                <div class="row p-3 border-bottom">
                    <span class="col-4 text-right profile-label">パスワードを変更</span>
                    <span class="col-8"><a href="{{ route('showUpdatePassword') }}" class="btn btn-primary text-white">パスワードを変更</a></span>
                </div>
                <div class="logout-btn text-right mt-2 mr-5 mb-5 ml-auto">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-outline-primary mb-2">ログアウト</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ mix('js/userConfig.js') }}"></script>
@endsection