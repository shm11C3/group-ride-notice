@extends('layouts.layout')
@section('title', '検索')
@section('content')
<div id="app" v-cloak>
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
    <div class="form-group">
        <input type="search" v-model="searchWord"  @keypress.prevent.enter.exact="enable_submit" @keyup.prevent.enter.exact="submit" class="form-control" aria-describedby="searchWordHelp" placeholder="検索">
        <small id="searchWordHelp" class="form-text text-muted">検索したいワードを入力してください</small>
    </div>
    <div v-if="!isLoad && !dataIsExist && canSubmit">
        <div class="alert alert-secondary" role="alert">
            検索結果はありません
        </div>
    </div>
    <div v-if="option == 0 || option == 1">
        <div v-if="rides.length">
            <h6 class="mt-4 ml-4">見つかったライド</h6>
            <div v-for="(ride, index) in rides" class="media ride shadow m-4">
                <a v-bind:href="'/user/'+ride.host_user_uuid">
                    <img class="bd-placeholder-img user_profile_img_s" v-bind:src="ride.user_profile_img_path">
                </a>
                <div class="media-body">
                    <div class="p-2">
                        <div class="btn-toolbar">
                            <div class="ride-title">
                                <a v-bind:href="'/user/'+ride.host_user_uuid" class="mb-1 username">@@{{ ride.user_name }}</a>
                                <a v-bind:href="'/ride?uuid='+ride.uuid">
                                    <h6 class="mb-0">@{{ ride.ride_name }}</h6>
                                </a>
                            </div>
                            <div class="btn-group ml-auto">
                                @auth
                                <div v-if="ride.hadByLoginUser">
                                    <a class="btn btn-success mb-1 mt-1" v-bind:href="'/my-ride?uuid=' + ride.uuid">ライド管理</a>
                                </div>
                                <div v-else>
                                    <div v-if="ride.rideParticipant_user">
                                        <a class="btn btn-success mb-1 mt-1" v-bind:href="'/ride?uuid=' + ride.uuid">参加登録済み</a>
                                    </div>
                                    <div v-else>
                                        <a class="btn btn-secondary mb-1 mt-1" v-bind:href="'/ride?uuid=' + ride.uuid">参加未登録</a>
                                    </div>
                                </div>
                                @endauth
                                @guest
                                <a class="btn btn-success mb-1 mt-1" href="{{ route('showLogin') }}">ログインして参加する</a>
                                @endguest
                            </div>
                        </div>
                    </div>
                    <div class="pl-3 mt-5 mb-5">
                        <div class="course-profile">
                            <div v-if="ride.num_of_laps > 0">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <p>
                                            <span class="text-muted additional-txt">コース</span><br>
                                            @{{ ride.rr_name }} @{{ ride.num_of_laps }}周
                                        </p>
                                    </div>
                                    <div class="col-sm-4">
                                        <p>
                                            <span class="text-muted additional-txt">走行距離</span><br>
                                            @{{ ride.num_of_laps*ride.distance }}km
                                        </p>
                                    </div>
                                    <div class="col-sm-4">
                                        <p>
                                            <span class="text-muted additional-txt">獲得標高</span><br>
                                            @{{ ride.num_of_laps*ride.elevation }}m
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <p>
                                            <span class="text-muted additional-txt">ルート</span><br>
                                            @{{ ride.rr_name }}
                                        </p>
                                    </div>
                                    <div class="col-sm-4">
                                        <p>
                                            <span class="text-muted additional-txt">走行距離</span><br>
                                            @{{ ride.distance }}km
                                        </p>
                                    </div>
                                    <div class="col-sm-4">
                                        <p>
                                            <span class="text-muted additional-txt">獲得標高</span><br>
                                            @{{ ride.elevation }}m
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div  style="border-bottom: 1px solid rgb(219, 219, 219); margin-right: 5.2rem; margin-top: 3rem;"></div>
                        <div class="row mt-5">
                            <div class="col-sm-4 col-lg-2">
                                <p>
                                    <span class="text-muted additional-txt">@{{ ride.time_appoint.substring(0,4) }}</span><br>
                                    @{{ ride.time_appoint.substring(5,7)+'月'+ride.time_appoint.substring(8,10)+'日'+ride.time_appoint.substring(10,16) }}
                                </p>
                            </div>
                            <div class="col-sm-4 col-lg-2">
                                <p>
                                    <span class="text-muted additional-txt">@{{ prefecture[ride.prefecture_code-1] }}</span><br>
                                    @{{ ride.mp_name }}集合
                                </p>
                            </div>
                            <div class="col-sm-4 col-lg-8">
                                <p>
                                    <span class="text-muted additional-txt">参加人数</span><br>
                                    @{{ ride.rideParticipant_count }}人
                                </p>
                            </div>
                        </div>
                        <p class="mb-0 mt-3">強度：@{{ ride.intensity }}</p>
                        <div class="progress" style="height: 10px; margin-right: 6rem">
                            <div class="progress-bar" role="progressbar" v-bind:style="'width: '+ride.intensity+'0%'" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="m-4">
                <div v-if="nextRidesIsExist && !acceptAddLoad">
                    <button v-on:click="addLoadBtn(1)" type="button" class="btn btn-success btn-lg btn-block">ライドを更に読み込む</button>
                </div>
                <div v-else-if="nextRidesIsExist">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-grow text-success mb-30" style="width: 3rem; height: 3rem; margin-top: 200px;" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div class="alert alert-secondary" role="alert">
                        最後の検索結果です。
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-if="option == 0 || option == 2">
        <div v-if="users.length">
            <h6 class="mt-4 ml-4">見つかったユーザー</h6>
            <div v-for="(user, i) in users" class="media shadow m-4">
                <a class=text-decoration-none v-bind:href="'user/'+user.user_uuid">
                    <img class="bd-placeholder-img user_profile_img_s" v-bind:src="user.user_profile_img_path">
                </a>
                <div class="media-body m-2">
                    <div class="btn-toolbar">
                        <div class="username-title">
                            <a class=text-decoration-none v-bind:href="'user/'+user.user_uuid">
                                <p>
                                    <span class="font-weight-bold">@{{ user.name }}</span><br>
                                    @{{ prefecture[user.prefecture_code-1] }}
                                </p>
                            </a>
                        </div>
                        @auth
                        <div class="btn-group ml-auto">
                            <div v-if="!user.userFollowed && user.uuid != '{{ Auth::getUser()->uuid }}'">
                                <button v-on:click="follow(user.uuid, i)" type="button" class="btn btn-success">フォローする</button>
                            </div>
                            <div v-else-if="user.userFollowed && user.uuid != '{{ Auth::getUser()->uuid }}'">
                                <button v-on:click="follow(user.uuid, i)" type="button" class="btn btn-outline-secondary">フォロー解除</button>
                            </div>
                            <div v-else>
                                <a type="button" class="btn btn-outline-secondary" href="{{ route('showConfig') }}">プロフィールを編集</a>
                            </div>
                        </div>
                        @endauth
                    </div>
                    <div class="user-intro border-top">
                        <span class="text-muted additional-txt">自己紹介</span><br>
                            <div>
                                @{{ user.user_intro }}
                            </div>
                        <p class="mt-2"></p>
                    </div>
                </div>
            </div>
            <div class="m-4">
                <div v-if="nextUsersIsExist && !acceptAddLoad">
                    <button v-on:click="addLoadBtn(2)" type="button" class="btn btn-success btn-lg btn-block">ユーザーを更に読み込む</button>
                </div>
                <div v-else-if="nextUsersIsExist">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-grow text-success mb-30" style="width: 3rem; height: 3rem; margin-top: 200px;" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div class="alert alert-secondary" role="alert">
                        最後の検索結果です。
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div ref="observe_element"></div>
</div>
<script src="{{ mix('js/search.js') }}"></script>
@endsection
