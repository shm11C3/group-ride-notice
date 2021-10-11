@extends('layouts.layout')
@section('title','ライド作成')
@section('content')
<h2>ライドを作成</h2>
<div id="app" v-cloak>
  
  <!--modal-->
  <div class="modal fade" id="rideRouteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">新しいルートを登録</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="rr_name">名前 ※必須</label>
            <input class="form-control" v-model="rr_name">
            <small class="form-text text-muted">わかりやすい名前を入力してください</small>
          </div>
          <div class="form-group">
            <label for="rr_elevation">獲得標高</label>
            <div class="input-group">
              <input class="form-control" v-model="rr_elevation">
              <div class="input-group-prepend">
                <div class="input-group-text">m</div>
              </div>
            </div>
            <small class="form-text text-muted">獲得標高を数値で入力してください</small>
          </div>
          <div class="form-group">
            <label for="rr_distance">距離 ※必須</label>
            <div class="input-group">
              <input class="form-control" v-model="rr_distance">
              <div class="input-group-prepend">
                <div class="input-group-text">km</div>
              </div>
            </div>
            <small class="form-text text-muted">距離を数値で入力してください</small>
          </div>

          <div class="form-group">
            <label for="rr_comment">ルート(コース)の説明 ※必須</label>
            <textarea class="form-control" rows="3" v-model="rr_comment"></textarea>
          </div>

          <fieldset class="form-group mt-2">
            <div class="form-check">
              <label class="form-check-label">
                <input v-model="rr_lap_status" class="form-check-input" type="checkbox" value="1" checked="">
                反復・周回コース
              </label>
            </div>
          </fieldset>

          <fieldset class="form-group mt-2">
            <div class="form-check">
              <label class="form-check-label">
                <input v-model="rr_save_status" class="form-check-input" type="checkbox" value="1" checked="">
                ルートを保存
              </label>
            </div>
          </fieldset>

          <div v-if="rr_save_status">
            <div class="btn-group btn-group-toggle status-radio-group" data-toggle="buttons">
              <label class="btn btn-outline-primary status-radio">
                  <input type="radio" name="publish_status" autocomplete="off" v-on:click="rr_inputPublishStatus('0')">公開
              </label>
              <label class="btn btn-outline-primary status-radio">
                  <input type="radio" name="publish_status" autocomplete="off" v-on:click="rr_inputPublishStatus('1')">限定公開
              </label>
              <label class="btn btn-outline-primary status-radio">
                  <input type="radio" name="publish_status" autocomplete="off" v-on:click="rr_inputPublishStatus('2')">非公開
              </label>
            </div>
          </div>

          <div v-if="rr_httpErrors">
            <div v-for="(rr_httpError, index) in rr_httpErrors">
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                @{{ rr_httpError }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <div v-if="rr_isPush">
            <button type="button" class="btn btn-primary" data-dismiss="modal">キャンセル</button>
            <button type="button" class="btn btn-success" disabled>
              <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
              送信中...
            </button>
          </div>
          <div v-else>
            <button type="button" class="btn btn-primary" data-dismiss="modal">キャンセル</button>
            <button type="button" class="btn btn-success" v-on:click="rr_submit">登録</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="meetingPlaceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">新しい集合場所を登録</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="mp_name">名前 ※必須</label>
            <input class="form-control" v-model="mp_name">
            <small class="form-text text-muted">わかりやすい名前を32文字以内で入力してください</small>
          </div>
          <div class="form-group">
            <label for="mp_prefecture_code">都道府県</label>
            <select v-on:change="mp_inputPrefecture_code" class="form-control">
              <option value="">選択してください</option>
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
          <div class="form-group">
            <label for="mp_address">場所の詳細 ※必須</label>
            <textarea class="form-control" rows="3" v-model="mp_address"></textarea>
            <small class="form-text text-muted">住所や建造物の固有名など、場所を特定できる情報を255文字以内で入力してください</small>
          </div>
          <fieldset class="form-group mt-2">
            <div class="form-check">
              <label class="form-check-label">
                <input v-model="mp_save_status" class="form-check-input" type="checkbox" value="1" checked="">
                集合場所を保存
              </label>
            </div>
          </fieldset>

          <div v-if="mp_save_status">
            <div class="btn-group btn-group-toggle status-radio-group" data-toggle="buttons">
              <label class="btn btn-outline-primary status-radio">
                  <input type="radio" name="publish_status" autocomplete="off" v-on:click="mp_inputPublishStatus('0')">公開
              </label>
              <label class="btn btn-outline-primary status-radio">
                  <input type="radio" name="publish_status" autocomplete="off" v-on:click="mp_inputPublishStatus('1')">限定公開
              </label>
              <label class="btn btn-outline-primary status-radio">
                  <input type="radio" name="publish_status" autocomplete="off" v-on:click="mp_inputPublishStatus('2')">非公開
              </label>
            </div>
          </div>

          <div v-if="mp_httpErrors">
            <div v-for="(mp_httpError, index) in mp_httpErrors">
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                @{{ mp_httpError }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div v-if="mp_isPush">
            <button type="button" class="btn btn-primary" data-dismiss="modal">キャンセル</button>
            <button type="button" class="btn btn-success" disabled>
              <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
              送信中...
            </button>
          </div>
          <div v-else>
            <button type="button" class="btn btn-primary" data-dismiss="modal">キャンセル</button>
            <button type="button" class="btn btn-success" v-on:click="mp_submit">登録</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--modal-->

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
  <div class="form-group mt-2">
    <label for="name">ライド名</label>
    <input v-model="name" type="text" name="name" v-bind:class="nameClass" placeholder="ライド名">
    <div class="invalid-feedback">@{{ nameErrComment }}</div>
  </div>
  <div v-if="selectedLap_status">
    <div class="row">
      <div class="col">
        <div class="form-group mt-2">
          <label for="meetingPlace">ルートを選択</label>
          <div v-if="rideRoutes.data">
            <select class="form-control" id="select1" v-model="selectedRideRouteKey">
              <option value="">選択してください</option>
              <option v-for="(rideRoute, index) in rideRoutes.data" v-bind:value="index">
                @{{ rideRoute.name }}
              </option>
              <option value="create"><span class="font-weight-bold">+ </span>新しいルートを作成</option>
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
          <option value="">選択してください</option>
          <option v-for="(rideRoute, index) in rideRoutes.data" v-bind:value="index">
            @{{ rideRoute.name }}
          </option>
          <option value="create"><span class="font-weight-bold">+ </span>新しいルートを作成</option>
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
    <label for="meetingPlace">集合場所を選択</label>
    <div v-if="meetingPlaces.data">
      <select class="form-control" id="select1" v-model="selectedMeetingPlace">
        <option value="">選択してください</option>
        <option v-for="(meetingPlace, index) in meetingPlaces.data" v-bind:value="meetingPlace.uuid">
          @{{ meetingPlace.name }}
        </option>
        <option value="create"><span class="font-weight-bold">+ </span>新しい集合場所を作成</option>
      </select>
    </div>
    <div v-else class="text-center">
      <div class="spinner-grow spinner-grow-sm text-success" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </div>
  </div>
  <div class="row mt-2">
    <div class="col">
      <div class="form-group">
        <label for="date">開催日　※1年後まで</label>
        <input class="form-control" type="date" v-model="date">
      </div>
    </div>
    <div class="col">
      <div class="form-group">
        <label for="time">集合時間</label>
        <input class="form-control" type="time" v-model="time">
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
        <span class="intst-display">強度：<span v-bind:class="intensityStyle[showIntensityInfo]">@{{ intensity }}</span></span>
        <i v-bind:class="'fas fa-biking '+intensityStyle[showIntensityInfo]"></i><br>
        @{{ intensityComment[showIntensityInfo] }}
      </div>
    </div>
  </div>
  <div class="btn-group btn-group-toggle status-radio-group mt-2" data-toggle="buttons">
    <label class="btn btn-outline-primary status-radio">
        <input type="radio" name="publish_status" autocomplete="off" v-on:click="inputPublishStatus('0')" checked>公開
    </label>
    <label class="btn btn-outline-primary status-radio">
        <input type="radio" name="publish_status" autocomplete="off" v-on:click="inputPublishStatus('1')">限定公開
    </label>
    <label class="btn btn-outline-primary status-radio">
        <input type="radio" name="publish_status" autocomplete="off" v-on:click="inputPublishStatus('2')">非公開
    </label>
  </div>

  <div class="text-right mt-2 mr-5 mb-5">
    <div v-if="isPush">
      <button type="submit" class="btn btn-success" disabled>
        <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
        送信中...
      </button>
    </div>
    <div v-else>
      <button type="submit" class="btn btn-success" v-on:click="submit" v-bind:disabled="disableSubmitBtn">送信</button>
    </div>
  </div>

</div>
<script src="{{ mix('js/createRideForm.js') }}"></script>
@endsection