@extends('layouts.layout')
@section('title', '検索')
@section('content')
<div id="app">
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
    <div v-if="isLoad">
        <div class="d-flex justify-content-center">
            <div class="spinner-grow text-success mb-30" style="width: 3rem; height: 3rem; margin-top: 200px;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
    <div v-else>
        <div v-if="dataIsExist">
            <div class="alert alert-secondary" role="alert">
                検索結果はありません
            </div>
        </div>
    </div>
    <div v-if="rides.length">
        <h6 class="mt-4 ml-4">見つかったライド</h6>
        <div v-for="(ride, index) in rides" class="media ride shadow m-4">
            <a v-bind:href="'/user/'+ride.host_user_uuid">
                <svg class="bd-placeholder-img align-self-start profile-img" width="64" height="64" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Placeholder: 64x64"><title>Placeholder</title><rect width="100%" height="100%" fill="#868e96"/><text x="50%" y="50%" fill="#dee2e6" ></text></svg>
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
    </div>
    <div v-if="users.length">
        <h6 class="mt-4 ml-4">見つかったユーザー</h6>
        <div v-for="user in users" class="media shadow m-4">
            <a class=text-decoration-none v-bind:href="'user/'+user.user_uuid">
                <svg class="bd-placeholder-img align-self-start profile-img" width="64" height="64" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Placeholder: 64x64"><title>Placeholder</title><rect width="100%" height="100%" fill="#868e96"/><text x="50%" y="50%" fill="#dee2e6" ></text></svg>
                <div class="media-body m-2">
                    <div class="btn-toolbar">
                        <div class="username-title">
                            <p>
                                <span class="font-weight-bold">@{{ user.name }}</span><br>
                                @{{ prefecture[user.prefecture_code-1] }}
                            </p>
                        </div>
                    </div>
                    <div class="user-intro border-top">
                        <span class="text-muted additional-txt">自己紹介</span><br>
                            <div>
                                @{{ user.user_intro }}
                            </div>
                        <p class="mt-2"></p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
<script src="{{ mix('js/search.js') }}"></script>
@endsection