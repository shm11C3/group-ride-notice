@extends('layouts.layout')
@section('title','ホーム')
@section('content')
<div id="app" v-cloak>
<!--次の参加予定のライドを表示-->
    <div class="ride-schedule-group">
        @auth
        <h3 class="mt-2">次に参加予定のライド</h3>
        <div v-if="resNextIsExist">
            <div class="media ride shadow p-2 mt-4 mb-2">
                <div class="media-body p-2">
                    <div class="btn-toolbar">
                        <div class="ride-title">
                            <a v-bind:href="'../ride?uuid='+next_ride.uuid">
                                <h4 class="mb-0">@{{ next_ride.ride_name }}</h4>
                            </a>
                        </div>
                        <div class="btn-group ml-auto">
                            <div v-if="next_ride.host_user_uuid == authUser">
                                <a class="btn btn-success mb-1 mt-1" v-bind:href="'../my-ride?uuid=' + next_ride.uuid">ライド管理</a>
                            </div>
                            <div v-else>
                                <p class="btn btn-outline-success mb-1 mt-1" v-bind:href="'../ride?uuid=' + next_ride.uuid">参加ライド</p>
                            </div>
                        </div>
                    </div>
                    <div class="pl-3 mt-5 mb-5">
                        <div class="course-profile">
                            <div v-if="next_ride.num_of_laps > 0">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <p>
                                            <span class="text-muted additional-txt">コース</span><br>
                                            @{{ next_ride.rr_name }} @{{ next_ride.num_of_laps }}周
                                        </p>
                                    </div>
                                    <div class="col-sm-4">
                                        <p>
                                            <span class="text-muted additional-txt">走行距離</span><br>
                                            @{{ next_ride.num_of_laps*next_ride.distance }}km
                                        </p>
                                    </div>
                                    <div class="col-sm-4">
                                        <p>
                                            <span class="text-muted additional-txt">獲得標高</span><br>
                                            @{{ next_ride.num_of_laps*next_ride.elevation }}m
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <p>
                                            <span class="text-muted additional-txt">ルート</span><br>
                                            @{{ next_ride.rr_name }}
                                        </p>
                                    </div>
                                    <div class="col-sm-4">
                                        <p>
                                            <span class="text-muted additional-txt">走行距離</span><br>
                                            @{{ next_ride.distance }}km
                                        </p>
                                    </div>
                                    <div class="col-sm-4">
                                        <p>
                                            <span class="text-muted additional-txt">獲得標高</span><br>
                                            @{{ next_ride.elevation }}m
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div  style="border-bottom: 1px solid rgb(219, 219, 219); margin-right: 5.2rem; margin-top: 3rem;"></div>
                        <div class="row mt-5">
                            <div class="col-sm-5 col-lg-2">
                                <p>
                                    <span class="text-muted additional-txt">@{{ next_ride.time_appoint.substring(0,4) }}</span><br>
                                    @{{ next_ride.time_appoint.substring(5,7)+'月'+next_ride.time_appoint.substring(8,10)+'日'+next_ride.time_appoint.substring(10,16) }}
                                </p>
                            </div>
                            <div class="col-sm-5 col-lg-2">
                                <p>
                                    <span class="text-muted additional-txt">@{{ prefecture[next_ride.prefecture_code-1] }}</span><br>
                                    @{{ next_ride.mp_name }}集合
                                </p>
                            </div>
                            <div class="col-sm-2 col-lg-8">

                            </div>
                        </div>
                        <p class="mb-0 mt-3">強度：@{{ next_ride.intensity }}</p>
                        <div class="progress" style="height: 10px; margin-right: 6rem">
                            <div class="progress-bar" role="progressbar" v-bind:style="'width: '+next_ride.intensity+'0%'" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="next_isLoad">
            <div class="d-flex justify-content-center">
                <div class="spinner-grow text-success mb-30" style="width: 3rem; height: 3rem; margin-top: 200px;" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>
        <div v-if="!resNextIsExist && !next_isLoad">
            <div class="alert alert-info alert-dismissible fade show mt-5" role="alert">
                次に参加するライドはありません
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
        @endauth
        @guest
        <h3>次に参加予定のライド</h3>
        <div class="alert alert-info mt-2" role="alert">
            <p>ライドの参加には<a href="{{ route('showLogin') }}" class="alert-link">ログイン</a>が必須です</p>
        </div>
        @endguest
    </div>
    <!--一覧系表示-->
    <div class="rides-group mt-5">
        <h3 class="mb-4">ライド一覧</h3>
        <div class="row" style="margin-left: 0.1rem;">
            <div class="col-12 col-lg-3 form-group mr-1">
                <div class="row mr-2">
                    <label class="col-4 col-lg-12" for="time_appoint">開催日時</label>
                    <select v-on:change="input_time_appoint" v-bind:disabled="isLoad" class="form-control col-8 col-lg-12">
                        <option value="0">すべて表示</option>
                        <option value="1">本日開催</option>
                        <option value="2">明日開催</option>
                        <option value="3">1週間以内開催</option>
                        <option value="4">1ヶ月以内開催</option>
                        <option value="5">開催終了</option>
                    </select>
                </div>
            </div>
        <div class="col-12 col-lg-3 form-group mr-1">
            <div class="row mr-2">
                <label class="col-4 col-lg-12" for="prefecture_code">都道府県</label>
                <select v-on:change="input_prefecture_code" v-bind:disabled="isLoad" class="form-control col-8 col-lg-12">
                    <option value="0">すべて表示</option>
                    <option value="1">北海道</option>
                    <option value="2">青森県</option>
                    <option value="3">岩手県</option>
                    <option value="4">宮城県</option>
                    <option value="5">秋田県</option>
                    <option value="6">山形県</option>
                    <option value="7">福島県</option>
                    <option value="8">茨城県</option>
                    <option value="9">栃木県</option>
                    <option value="10">群馬県</option>
                    <option value="11">埼玉県</option>
                    <option value="12">千葉県</option>
                    <option value="13">東京都</option>
                    <option value="14">神奈川県</option>
                    <option value="15">新潟県</option>
                    <option value="16">富山県</option>
                    <option value="17">石川県</option>
                    <option value="18">福井県</option>
                    <option value="19">山梨県</option>
                    <option value="20">長野県</option>
                    <option value="21">岐阜県</option>
                    <option value="22">静岡県</option>
                    <option value="23">愛知県</option>
                    <option value="24">三重県</option>
                    <option value="25">滋賀県</option>
                    <option value="26">京都府</option>
                    <option value="27">大阪府</option>
                    <option value="28">兵庫県</option>
                    <option value="29">奈良県</option>
                    <option value="30">和歌山県</option>
                    <option value="31">鳥取県</option>
                    <option value="32">島根県</option>
                    <option value="33">岡山県</option>
                    <option value="34">広島県</option>
                    <option value="35">山口県</option>
                    <option value="36">徳島県</option>
                    <option value="37">香川県</option>
                    <option value="38">愛媛県</option>
                    <option value="39">高知県</option>
                    <option value="40">福岡県</option>
                    <option value="41">佐賀県</option>
                    <option value="42">長崎県</option>
                    <option value="43">熊本県</option>
                    <option value="44">大分県</option>
                    <option value="45">宮崎県</option>
                    <option value="46">鹿児島県</option>
                    <option value="47">沖縄県</option>
                </select>
            </div>
        </div>
        <div class="col-12 col-lg-3 form-group">
            <div class="row mr-2">
                <label class="col-4 col-lg-12" for="intensity">強度</label>
                <select v-on:change="input_intensity" v-bind:disabled="isLoad" class="form-control col-8 col-lg-12">
                    <option value="0">すべて表示</option>
                    <option value="1">かなり緩め</option>
                    <option value="2">低強度</option>
                    <option value="3">中強度</option>
                    <option value="4">高強度</option>
                </select>
            </div>
        </div>
        <div class="col-12 col-lg-3">
        </div>
    </div>
    <div class="mt-2">
        <input v-on:change="input_filterFollow" value="true" v-bind:disabled="isLoad" type="checkbox" v-model="filterFollow" @guest disabled @endguest>
        <label for="filterFollow" class="ml-1">フォロー中のユーザのみ表示</label>
    </div>
    <div class="rides">
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
            @auth
            <div class="modal fade" id="cancelParticipateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">参加をキャンセル</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            キャンセルしますか？
                        </div>
                        <div class="modal-footer">
                            <div v-if="pt_isPush">
                                <button type="button" class="btn btn-secondary" disabled>いいえ</button>
                                <button type="button" class="btn btn-primary" disabled>
                                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                    送信中...
                                </button>
                            </div>
                            <div v-else>
                                <button type="button" class="btn btn-primary" data-dismiss="modal">いいえ</button>
                                <button type="button" v-on:click="cancelParticipation" class="btn btn-success">はい</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--ライド参加時のモーダル-->
            <div v-if="participateModal">
                <div class="model-group">
                    <div class="modal fade" id="participateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">ライドに参加する</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>
                                        <span class="text-muted additional-txt">ライド名</span><br>
                                        @{{ rides[participateIndex].ride_name }}</p>
                                    <p>
                                        <span class="text-muted additional-txt">ホストユーザー</span><br>
                                        @{{ rides[participateIndex].user_name }}</p>
                                    <p>
                                        <span class="text-muted additional-txt">ルート</span><br>
                                        @{{ rides[participateIndex].rr_name }}</p>
                                    <p>
                                        <span class="text-muted additional-txt">ライドの説明</span><br>
                                        @{{ rides[participateIndex].ride_comment }}</p>
                                    <p class="mt-3">
                                        <span class="text-muted additional-txt">@{{ rides[participateIndex].time_appoint.substring(0,4) }}</span><br>
                                        @{{ rides[participateIndex].time_appoint.substring(5,7)+'月'+rides[participateIndex].time_appoint.substring(8,10)+'日'+rides[participateIndex].time_appoint.substring(10,16) }}
                                    </p>
                                    <span class="text-muted additional-txt">メッセージ</span>
                                    <textarea class="form-control" v-model="participateComment"></textarea>

                                </div>
                                <div class="modal-footer">
                                    <div v-if="pt_isPush">
                                        <button type="button" class="btn btn-primary" disabled>キャンセル</button>
                                        <button type="button" class="btn btn-success" disabled>
                                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                            送信中...
                                        </button>
                                    </div>
                                    <div v-else>
                                        <button type="button" class="btn btn-primary" data-dismiss="modal">キャンセル</button>
                                        <button type="button" v-on:click="participation" class="btn btn-success">送信</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endauth
            <!---->
            <div v-for="(ride, index) in rides" class="media ride shadow mt-4">
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
                                        <button class="btn btn-success mb-1 mt-1" v-on:click="openCancelParticipateModal(index)" data-toggle="modal" data-target="#participateModal">参加をキャンセル</button>
                                    </div>
                                    <div v-else>
                                        <button class="btn btn-success mb-1 mt-1" v-on:click="openParticipateModal(index)" data-toggle="modal" data-target="#participateModal">参加する</button>
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
        <div ref="observe_element"></div>
            <div v-if="isLoad">
                <div class="d-flex justify-content-center">
                    <div class="spinner-grow text-success mb-30" style="width: 3rem; height: 3rem; margin-top: 200px;" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
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
<script src="{{ mix('js/home.js') }}"></script>
@endsection
