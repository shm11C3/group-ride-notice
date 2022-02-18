@extends('layouts.layout')
@section('title', '参加予定ライド')
@section('content')
<h3>参加予定のライド</h3>
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
    <div v-if="rides.length!=0">
        <div v-for="(ride, i in rides" class="media ride shadow p-2 mt-4 mb-2">
            <div class="media-body p-2">
                <div class="btn-toolbar">
                    <div class="ride-title">
                        <a v-bind:href="'../ride?uuid='+ride.uuid">
                            <h4 class="mb-0">@{{ ride.ride_name }}</h4>
                        </a>
                    </div>
                    <div class="btn-group ml-auto">
                        <div v-if="ride.host_user_uuid == authUser">
                            <a class="btn btn-success mb-1 mt-1" v-bind:href="'../my-ride?uuid=' + ride.uuid">ライド管理</a>
                        </div>
                        <div v-else>
                            <a class="btn btn-outline-success mb-1 mt-1" v-bind:href="'../ride?uuid=' + ride.uuid">詳細を見る</a>
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
                    <div v-if="ride.map_img_uri" class="route-img-div">
                        <div v-if="!isImgLoaded[i]">
                            <div class="d-flex justify-content-center load-img">
                                <div class="spinner-grow text-success mb-30" style="width: 3rem; height: 3rem; margin-top: 200px;" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <img v-bind:src="ride.map_img_uri" v-on:load="load_img(i)" v-bind:class="'route-img '+opacity[0]" alt="ルートマップ">
                    </div>
                    <div  style="border-bottom: 1px solid rgb(219, 219, 219); margin-right: 5.2rem; margin-top: 3rem;"></div>
                    <div class="row mt-5">
                        <div class="col-sm-5 col-lg-2">
                            <p>
                                <span class="text-muted additional-txt">@{{ ride.time_appoint.substring(0,4) }}</span><br>
                                @{{ ride.time_appoint.substring(5,7)+'月'+ride.time_appoint.substring(8,10)+'日'+ride.time_appoint.substring(10,16) }}
                            </p>
                        </div>
                        <div class="col-sm-5 col-lg-2">
                            <p>
                                <span class="text-muted additional-txt">@{{ prefecture[ride.prefecture_code-1] }}</span><br>
                                @{{ ride.mp_name }}集合
                            </p>
                        </div>
                        <div class="col-sm-2 col-lg-8">

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
    <div ref="observe_element"></div>
    <div v-if="isLoad">
        <div class="d-flex justify-content-center">
            <div class="spinner-grow text-success mb-30" style="width: 3rem; height: 3rem; margin-top: 200px;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
    <div v-if="!resIsExist && !isLoad">
        <div class="alert alert-warning alert-dismissible fade show mt-5" role="alert">
            データが存在しません
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
</div>
<script src="{{ mix('js/participated.js') }}"></script>
@endsection
