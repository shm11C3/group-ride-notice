@extends('layouts.layout')
@section('title','ライド管理')
@section('content')
<h2>ライド管理</h2>
<div id="app">
    <div v-if="ride.length">
        <div class="row">
            <div class="pl-3 mt-5 mb-5 col">
                <div class="course-profile">
                    <div v-if="ride[0].num_of_laps > 0">
                        <div class="row">
                            <div class="col-4">
                                <p>
                                    <span class="text-muted additional-txt">コース</span><br>
                                    @{{ ride[0].rr_name }} @{{ ride[0].num_of_laps }}周
                                </p>
                            </div>
                            <div class="col-4">
                                <p>
                                    <span class="text-muted additional-txt">走行距離</span><br>
                                    @{{ ride[0].num_of_laps*ride[0].distance }}km
                                </p>
                            </div>
                            <div class="col-4">
                                <p>
                                    <span class="text-muted additional-txt">獲得標高</span><br>
                                    @{{ ride[0].num_of_laps*ride[0].elevation }}m
                                </p>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div class="row">
                            <div class="col-4">
                                <p>
                                    <span class="text-muted additional-txt">ルート</span><br>
                                    @{{ ride[0].rr_name }}
                                </p>
                            </div>
                            <div class="col-4">
                                <p>
                                    <span class="text-muted additional-txt">走行距離</span><br>
                                    @{{ ride[0].distance }}km
                                </p>
                            </div>
                            <div class="col-4">
                                <p>
                                    <span class="text-muted additional-txt">獲得標高</span><br>
                                    @{{ ride[0].elevation }}m
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="border-bottom: 1px solid rgb(219, 219, 219); margin-right: 5.2rem; margin-top: 3rem;"></div>
                <div class="mt-5">
                    <p>
                        <span class="text-muted additional-txt">@{{ ride[0].time_appoint.substring(0,4) }}</span><br>
                        @{{ ride[0].time_appoint.substring(5,7)+'月'+ride[0].time_appoint.substring(8,10)+'日'+ride[0].time_appoint.substring(10,16) }}
                    </p>
                    <p>
                        <span class="text-muted additional-txt">@{{ prefecture[ride[0].prefecture_code-1] }}</span><br>
                        @{{ ride[0].mp_name }}集合
                    </p>
                    <p>
                        <span class="text-muted additional-txt">参加人数</span><br>
                        @{{ ride[0].rideParticipant_count }}人
                    </p>
                </div>
                <p class="mb-0 mt-3">強度：@{{ ride[0].intensity }}</p>
                <div class="progress" style="height: 10px; margin-right: 6rem">
                    <div class="progress-bar" role="progressbar" v-bind:style="'width: '+ride[0].intensity+'0%'" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <div class="col">

            </div>
        </div>
    </div>
    <div v-else>

    </div>
</div>
<script src="{{ mix('js/rideAdmin.js') }}"></script>
@endsection