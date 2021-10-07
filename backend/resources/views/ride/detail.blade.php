@extends('layouts.layout')
@section('title', e($ride->name))
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
    <div v-if="isLoad">
        <div class="d-flex justify-content-center">
            <div class="spinner-grow text-success mb-30" style="width: 3rem; height: 3rem; margin-top: 200px;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
    <div v-if="ride">
        <div class="mt-5 mb-5">
            <h4 class="d-flex">
                ライド概要
                <span class="text-muted additional-txt align-items-start ml-1 mt-1">
                    <i v-bind:class="'fas '+publish_icon[ride.publish_status]"></i>
                </span>
                <button class="hidden-btn ml-2" v-on:click="popUpTweetWindow">
                    <i class="fab fa-twitter-square twitter"></i>
                </button>
            </h4>
            <div class="course-profile mt-4">
                <div v-if="ride.num_of_laps > 0">
                    <p class="mb-0 username">@@{{ ride.ride_participants[0].user.name }}</p>
                    <p>
                        <span class="text-muted additional-txt">ライド名</span><br>
                        @{{ ride.ride_name }}
                    </p>
                    <div class="row">
                        <div class="col-4">
                            <p>
                                <span class="text-muted additional-txt">コース</span><br>
                                @{{ ride.rr_name }} @{{ ride.num_of_laps }}周
                            </p>
                        </div>
                        <div class="col-4">
                            <p>
                                <span class="text-muted additional-txt">走行距離</span><br>
                                @{{ ride.num_of_laps*ride.distance }}km
                            </p>
                        </div>
                        <div class="col-4">
                            <p>
                                <span class="text-muted additional-txt">獲得標高</span><br>
                                @{{ ride.num_of_laps*ride.elevation }}m
                            </p>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <p class="mb-0 username">@@{{ ride.ride_participants[0].user.name }}</p>
                    <p>
                        <span class="text-muted additional-txt">ライド名</span><br>
                        @{{ ride.ride_name }}
                    </p>
                    <div class="row">
                        <div class="col-4">
                            <p>
                                <span class="text-muted additional-txt">ルート</span><br>
                                @{{ ride.rr_name }}
                            </p>
                        </div>
                        <div class="col-4">
                            <p>
                                <span class="text-muted additional-txt">走行距離</span><br>
                                @{{ ride.distance }}km
                            </p>
                        </div>
                        <div class="col-4">
                            <p>
                                <span class="text-muted additional-txt">獲得標高</span><br>
                                @{{ ride.elevation }}m
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div style="border-bottom: 1px solid rgb(219, 219, 219); margin-right: 5.2rem; margin-top: 3rem;">
                <!--border-line-->
            </div>
            <div class="mt-5">
                <p class="mb-0">
                    <span class="text-muted additional-txt">ルート</span><br>
                    @{{ ride.rr_name }}
                    <button class="hidden-btn" v-on:click="rr_showInfo">
                        <i class="fas fa-info-circle"></i>
                    </button>
                </p>
                <div v-if="rr_infoStatus" style="background-color: #f8f9fa; padding: 0.5rem;">
                    <button type="button" v-on:click="rr_showInfo" class="close" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="row">
                        <p class="col">
                            <span class="text-muted additional-txt">距離</span><br>
                            @{{ ride.distance }}km
                        </p>
                        <p class="col">
                            <span class="text-muted additional-txt">獲得標高</span><br>
                            @{{ ride.elevation }}m
                        </p>
                    </div>
                    <p>
                        <span class="text-muted additional-txt">コースの説明</span><br>
                        @{{ ride.rr_comment }}
                    </p>
                </div>
                <p class="mt-3">
                    <span class="text-muted additional-txt">@{{ ride.time_appoint.substring(0,4) }}</span><br>
                    @{{ time }}
                </p>
                <p class="mb-0">
                    <span class="text-muted additional-txt">@{{ prefecture[ride.prefecture_code-1] }}</span><br>
                    @{{ ride.mp_name }}集合
                    <button class="hidden-btn" v-on:click="mp_showInfo">
                        <i class="fas fa-info-circle"></i>
                    </button>
                </p>
                <div v-if="mp_infoStatus" style="background-color: #f8f9fa; padding: 0.5rem;">
                    <button type="button" v-on:click="mp_showInfo" class="close" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <p>
                        <span class="text-muted additional-txt">集合場所の説明</span><br>
                        @{{ ride.address }}
                    </p>
                    <span class="text-muted additional-txt">@{{ prefecture[ride.prefecture_code-1] }}の天気</span>
                    <div v-if="weathers.length">
                        <table class="table table-striped mt-2">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">今日</th>
                                <th scope="col">明日</th>
                                <th scope="col">明後日</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(weather, index) in weathers">
                                <th scope="row">@{{ weather.area.name }}</th>
                                <td>@{{ weather.weathers[0].replace(/\s+/g, "") }}<br>@{{ weather.winds[0].replace(/\s+/g, "") }}</td>
                                <td>@{{ weather.weathers[1].replace(/\s+/g, "") }}<br>@{{ weather.winds[1].replace(/\s+/g, "") }}</td>
                                <td>@{{ weather.weathers[2].replace(/\s+/g, "") }}<br>@{{ weather.winds[2].replace(/\s+/g, "") }}</td>
                              </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else>
                        <div class="d-flex justify-content-center">
                            <div class="spinner-grow text-success mb-30" style="width: 3rem; height: 3rem; margin-top: 200px;" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="mt-3">
                    <span class="text-muted additional-txt">参加人数</span><br>
                    @{{ ride.rideParticipant_count }}人
                </p>
                <p>
                    <span class="text-muted additional-txt">状態</span><br>
                    @{{ publish_status_arr[ride.publish_status] }}
                </p>
            </div>
            <div class="intensity"  style="margin-right: 6rem">
                <span class="text-muted additional-txt">強度</span><br>
                <span class="intst-display">
                    <span v-bind:class="intensityStyle[intensityInfo]">@{{ ride.intensity }}</span>
                </span>
                <i v-bind:class="'fas fa-biking '+intensityStyle[intensityInfo]"></i>
                <div class="progress" style="height: 10px;">
                    <div v-bind:class="'progress-bar '+intensityStyle[intensityInfo]" role="progressbar" v-bind:style="'width: '+ride.intensity+'0%'" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <br>
                @{{ intensityComment[intensityInfo] }}
            </div>
            <div style="border-bottom: 1px solid rgb(219, 219, 219); margin-right: 5.2rem; margin-top: 3rem;">
                <!--border-line-->
            </div>
            <p class="mt-1">
                <span class="text-muted additional-txt">ライドの説明</span><br>
                @{{ ride.ride_comment }}
            </p>
            <div style="border-bottom: 1px solid rgb(219, 219, 219); margin-right: 5.2rem; margin-top: 3rem;">
                <!--border-line-->
            </div>
            <div class="col mt-5 mb-5">
                <h4>参加者リスト</h4>
                <table class="table table-striped mt-2">
                    <thead class="thead-dark">
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">名前</th>
                        <th scope="col">コメント</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(ride_participant, index) in ride.ride_participants">
                        <th scope="row">@{{ index+1 }}</th>
                        <td>@{{ ride_participant.user.name }}</td>
                        <td>@{{ ride_participant.comment }}</td>
                      </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="{{ mix('js/rideDetail.js') }}"></script>
@endsection