@extends('layouts.layout')
@section('title','ユーザー')
@section('content')
@parent
<div id="app" v-cloak>
    <div class="media ride shadow m-3">
        <img class="bd-placeholder-img user_profile_img_m" src="{{ $user->user_profile_img_path }}">
        <div class="media-body m-2">
            <div class="btn-toolbar">
                <div class="username-title">
                    <p>
                        <span class="font-weight-bold">{{ '@'.$user->name }}</span><br>
                        {{ $prefecture }}
                    </p>
                </div>
                @auth
                @if(Auth::getUser()->uuid === $user->uuid)
                <div class="btn-group ml-auto mb-5">
                    <a type="button" class="btn btn-outline-secondary" href="{{ route('showConfig') }}">プロフィールを編集</a>
                </div>
                @else
                <div class="btn-group ml-auto mb-5">
                    <div @if($userFollowed) v-if="followBtnStatus" @else v-if="!followBtnStatus" @endif>
                        <button v-on:click="follow('{{ $user->uuid }}', false)" type="button" class="btn btn-outline-secondary">フォロー解除</button>
                    </div>
                    <div v-else>
                        <button v-on:click="follow('{{ $user->uuid }}', false)" type="button" class="btn btn-success">フォローする</button>
                    </div>
                </div>
                @endif
                @endauth
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
    <div class="user-info m-3">
        <ul class="nav nav-tabs user_info mt-5">
            <li class="nav-item">
                <button v-bind:class="'nav-link '+user_info_class[userRides_index]"  v-on:click="initialLoad(userRides_index)">ユーザーの主催ライド</button>
            </li>
            <li class="nav-item">
                <button v-bind:class="'nav-link '+user_info_class[follows_index]" v-on:click="initialLoad(follows_index)">フォロー中</button>
            </li>
            <li class="nav-item">
                <button v-bind:class="'nav-link '+user_info_class[followers_index]" v-on:click="initialLoad(followers_index)">フォロワー</button>
            </li>
        </ul>
        <div v-if="activeTab == userRides_index">
            <div v-for="(ride, index) in userRides" class="media ride shadow mt-4">
                <a v-bind:href="'/user/'+ride.host_user_uuid">
                    <img class="bd-placeholder-img user_profile_img_s" src="{{ $user->user_profile_img_path }}">
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
                                        <button class="btn btn-success mb-1 mt-1"  data-toggle="modal" data-target="#participateModal">参加をキャンセル</button>
                                    </div>
                                    <div v-else>
                                        <button class="btn btn-success mb-1 mt-1"  data-toggle="modal" data-target="#participateModal">参加する</button>
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
        <div v-else-if="activeTab == follows_index">
            <div v-for="(user, i) in follows" class="media shadow mt-4">
                <a class=text-decoration-none v-bind:href="user.uuid">
                    <img class="bd-placeholder-img user_profile_img_s" v-bind:src="user.user_profile_img_path">
                </a>
                <div class="media-body m-2">
                    <div class="btn-toolbar">
                        <div class="username-title">
                            <a class=text-decoration-none v-bind:href="user.uuid">
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
        </div>
        <div v-else>
            <div v-for="(user, i) in followers" class="media shadow mt-4">
                <a class=text-decoration-none v-bind:href="user.uuid">
                    <img class="bd-placeholder-img user_profile_img_s" v-bind:src="user.user_profile_img_path">
                </a>
                <div class="media-body m-2">
                    <div class="btn-toolbar">
                        <div class="username-title">
                            <a class=text-decoration-none v-bind:href="user.uuid">
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
        </div>
    </div>
    <div ref="observe_element"></div>
        <div v-if="next_is_exist">
            <div class="d-flex justify-content-center">
                <div class="spinner-grow text-success mb-30" style="width: 3rem; height: 3rem; margin-top: 200px;" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div></div>
</div>
<script src="{{ mix('js/userProfile.js') }}"></script>
@endsection
