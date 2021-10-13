@extends('layouts.layout')
@section('title','ライド管理')
@section('content')
<h2>ライド管理</h2>
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
    <div v-if="update">
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>更新しました！</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
    </div>
    <div v-if="isLoad">
        <div class="d-flex justify-content-center">
            <div class="spinner-grow text-success mb-30" style="width: 3rem; height: 3rem; margin-top: 200px;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
    <div v-if="!updateForm">
        <div v-if="ride.length">
            <div class="row m-2">
                <div class="sm-col mt-5 mb-5">
                    <h4>ライド概要 <button class="hidden-btn" v-on:click="openUpdate"><i class="fas fa-edit edit-btn"></i></button></h4>
                    <div class="course-profile">
                        <div v-if="ride[0].num_of_laps > 0">
                            <a v-bind:href="'/ride?uuid='+ride[0].uuid">
                                <span class="text-muted additional-txt">ライド名</span><br>
                                @{{ ride[0].ride_name }}
                            </a>
                            <p>
                                <span class="text-muted additional-txt">コース</span><br>
                                @{{ ride[0].rr_name }} @{{ ride[0].num_of_laps }}周
                            </p>
                            <div class="row">
                                <div class="col-6">
                                    <p>
                                        <span class="text-muted additional-txt">走行距離</span><br>
                                        @{{ ride[0].num_of_laps*ride[0].distance }}km
                                    </p>
                                </div>
                                <div class="col-6">
                                    <p>
                                        <span class="text-muted additional-txt">獲得標高</span><br>
                                        @{{ ride[0].num_of_laps*ride[0].elevation }}m
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <p>
                                <a v-bind:href="'/ride?uuid='+ride[0].uuid">
                                    <span class="text-muted additional-txt">ライド名</span><br>
                                    @{{ ride[0].ride_name }}
                                </a>
                            </p>
                            <p>
                                <span class="text-muted additional-txt">ルート</span><br>
                                @{{ ride[0].rr_name }}
                            </p>
                            <div class="row">
                                <div class="col-6">
                                    <p>
                                        <span class="text-muted additional-txt">走行距離</span><br>
                                        @{{ ride[0].distance }}km
                                    </p>
                                </div>
                                <div class="col-6">
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
                        <p>
                            <span class="text-muted additional-txt">状態</span><br>
                            @{{ publish_status_arr[ride[0].publish_status] }}
                        </p>
                    </div>
                        <span class="text-muted additional-txt">強度</span><br>
                        <span class="intst-display"><span v-bind:class="intensityStyle[intensityInfo]">@{{ ride[0].intensity }}</span></span>
                        <i v-bind:class="'fas fa-biking '+intensityStyle[intensityInfo]"></i>
                        <div class="progress" style="height: 10px; margin-right: 6rem">
                            <div v-bind:class="'progress-bar '+intensityStyle[intensityInfo]" role="progressbar" v-bind:style="'width: '+ride[0].intensity+'0%'" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <br>
                        @{{ intensityComment[intensityInfo] }}
                </div>
                <div class="sm-col mt-5 mb-5">
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
                          <tr v-for="(ride_participant, index) in ride[0].ride_participants">
                            <th scope="row">@{{ index+1 }}</th>
                            <td>@{{ ride_participant.user.name }}</td>
                            <td>@{{ ride_participant.comment }}</td>
                          </tr>
                        </tbody>
                      </table>
                </div>
            </div>
            <p>
                <span class="text-muted additional-txt">ライドに関する説明</span><br>
                @{{ ride[0].ride_comment }}
            </p>
            <span class="text-muted additional-txt">公開設定を変更</span><br>
            <div class="btn-toolbar" role="toolbar">          
                <div class="btn-group btn-group-toggle status-radio-group mt-2" data-toggle="buttons">
                    <label v-bind:class="'btn btn-outline-primary status-radio '+publish_status_class[0]">
                        <input type="radio" name="publish_status" autocomplete="off" v-on:click="updatePublishStatus('0')">公開
                    </label>
                    <label v-bind:class="'btn btn-outline-primary status-radio '+publish_status_class[1]">
                        <input type="radio" name="publish_status" autocomplete="off" v-on:click="updatePublishStatus('1')">限定公開
                    </label>
                    <label v-bind:class="'btn btn-outline-primary status-radio '+publish_status_class[2]">
                        <input type="radio" name="publish_status" autocomplete="off" v-on:click="updatePublishStatus('2')">非公開
                    </label>
                </div>
                <div class="btn-group ml-auto">
                    <button type="submit" class="btn btn-success m-1" v-on:click="openUpdate">編集する</button>
                </div>
            </div>
        </div>
        <div v-else>
            <div ref="observe_element"></div>
                <div v-if="!isLoad">
                    <div class="alert alert-warning alert-dismissible fade show mt-5" role="alert">
                        データが存在しません
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div v-if="authError">
                    <div class="alert alert-danger alert-dismissible fade show mt-5" role="alert">
                        IDが無効です
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
        <!--更新フォーム-->
            <h4 class="mt-5 mb-5">ライド情報を更新 <button class="hidden-btn" v-on:click="closeUpdate"><i class="fas fa-reply edit-btn"></i></button></h4>
            <div class="form-group mt-2">
                <label for="name">ライド名</label>
                <input v-model="name" type="text" name="name" class="form-control" placeholder="ライド名">
            </div>
            <div v-if="selectedLap_status">
                <div class="row">
                    <div class="col">
                        <div class="form-group mt-2">
                            <label for="meetingPlace">ルートを選択</label>
                            <div v-if="rideRoutes.data">
                                <select class="form-control" id="select1" v-model="selectedRideRouteKey">
                                    <option v-for="(rideRoute, index) in rideRoutes.data" v-bind:value="index">
                                        @{{ rideRoute.name }}
                                    </option>
                                </select>
                            </div>
                            <div v-else class="text-center">
                                <div class="spinner-grow spinner-grow-sm text-success" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group mt-2">
                            <label for="num_of_laps">周回・反復数</label>
                            <select class="form-control" v-model="num_of_laps">
                                <option value="0">選択してください</option>
                                <option v-for="n of 255" :key="n" v-bind:value="n">
                                    @{{ n }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
                <div class="form-group mt-2">
                    <label for="meetingPlace">ルートを選択</label>
                    <div v-if="rideRoutes.data">
                        <select class="form-control" id="select1" v-model="selectedRideRouteKey">
                            <option v-for="(rideRoute, index) in rideRoutes.data" v-bind:value="index">
                                @{{ rideRoute.name }}
                            </option>
                        </select>
                    </div>
                    <div v-else class="text-center">
                        <div class="spinner-grow spinner-grow-sm text-success" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>  
                </div>
            </div>
            <div class="form-group mt-2">
                <label for="comment">ライドの説明</label>
                <textarea v-bind:class="commentClass" id="comment" rows="3" v-model="comment"></textarea>
                <div class="invalid-feedback">@{{ commentErrComment }}</div>
            </div>
            <div class="row">
                <div class="form-group col-12 col-lg-6">
                    <label for="intensityControlRange">
                        ライドの強度(ペース)<br>
                        ※トレーニングの場合はメインセットに合わせて選択してください
                    </label>
                    <input type="range" class="intensity-range" min="0" max="10" value="0" id="intensityControlRange" v-model="intensity">
                </div>
                <div class="col-12 col-lg-6">
                    <div class="alert alert-secondary intst-background" role="alert">
                      <span class="intst-display">強度：<span v-bind:class="intensityStyle[intensityInfo]">@{{ intensity }}</span></span>
                      <i v-bind:class="'fas fa-biking '+intensityStyle[intensityInfo]"></i><br>
                      @{{ intensityComment[intensityInfo] }}
                    </div>
                </div>
            </div>
            <div class="text-right mt-2 mr-5 mb-5 ml-auto">
                <div v-if="isPush">
                    <button type="button" class="btn btn-primary" disabled>キャンセル</button>
                  <button type="submit" class="btn btn-success" disabled>
                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                    送信中...
                  </button>
                </div>
                <div v-else>
                  <button type="button" v-on:click="closeUpdate" class="btn btn-primary">キャンセル</button>
                  <button type="submit" class="btn btn-success" v-on:click="submit" v-bind:disabled="disableSubmitBtn">送信</button>
                </div>
            </div>
        </div>     
    </div>
</div>
<script src="{{ mix('js/rideAdmin.js') }}"></script>
@endsection