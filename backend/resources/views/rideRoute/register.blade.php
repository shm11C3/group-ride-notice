@extends('layouts.layout')
@section('title', '集合場所登録')
@section('content')
    <a class="btn btn-primary mb-4" href="{{ route('showMeetingPlaceRegisterForm') }}">集合場所登録へ</a>
<div id="app" v-cloak>
    <div class="radio-btns">
        <div class="form-check">
            <input v-model="lap_status_request" value="1" class="form-check-input" type="radio" checked>
            <label class="form-check-label" for="lap_status_request">
                周回コース
            </label>
        </div>
        <div class="form-check">
            <input v-model="lap_status_request" value="0" class="form-check-input" type="radio">
            <label class="form-check-label" for="lap_status_request">
                ラインコース
            </label>
        </div>
        <div class="form-check">
            <input v-model="lap_status_request" value="3" class="form-check-input" type="radio">
            <label class="form-check-label" for="lap_status_request">
                STRAVAから取得
            </label>
        </div>
    </div>
    <div v-if="rideRoutes.length">
        <div v-for="(rr, i) in rideRoutes" class="shadow m-2 p-1">
            <div class="media-body m-2 ">
                <div class="btn-toolbar">
                    <div class="rr_name">
                        <h5>@{{ rr.data.name }}</h5>
                    </div>
                    <div class="text-right ml-auto m-1">
                        <div v-if="rr.isRegistered">
                            <button v-on:click="saveRideRoute(i)" v-bind:disabled="isPush" type="button" class="btn btn-secondary">保存解除</button>
                        </div>
                        <div v-else>
                            <button v-on:click="saveRideRoute(i)" v-bind:disabled="isPush" type="button" class="btn btn-success">保存する</button>
                        </div>
                    </div>
                </div>
                <div v-if="rr.data.map_img_uri" class="route-img-div">
                    <div v-if="!isImgLoaded[i]">
                        <div class="d-flex justify-content-center load-img">
                            <div class="spinner-grow text-success mb-30" style="width: 3rem; height: 3rem; margin-top: 200px;" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <img v-bind:src="rr.data.map_img_uri" v-on:load="load_img(i)" v-bind:class="'route-img '+opacity[0]" alt="ルートマップ">
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <p>
                            <span class="text-muted additional-txt">走行距離</span><br>
                            @{{ rr.data.distance }}km
                        </p>
                    </div>
                    <div class="col-sm-6">
                        <p>
                            <span class="text-muted additional-txt">獲得標高</span><br>
                            @{{ rr.data.elevation }}m
                        </p>
                    </div>
                </div>
                <span class="text-muted additional-txt">ルートの説明</span>
                <p style="white-space:pre-wrap; word-wrap:break-word;">@{{ rr.data.comment  }}</p>
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
    <div v-else>
        <div v-if="!resIsExist && !isLoad">
            <div class="alert alert-secondary m-2" role="alert">
                データがありません
            </div>
        </div>
    </div>
</div>
<div class="mt-5"></div>
<script src="{{ mix('js/registerRideRoute.js') }}"></script>
@endsection
