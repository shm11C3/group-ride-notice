@extends('layouts.layout')
@section('title', '集合場所登録')
@section('content')
<div id="app" v-cloak>
    <div class="col-12 col-lg-3 form-group mr-1">
        <div class="mr-2">
            <label for="prefecture_code">都道府県</label>
            <select v-on:change="input_prefecture_code" v-bind:disabled="isLoad" class="form-control">
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
    <div v-if="meetingPlaces.length > 0">
        <div v-if="saveMeetingPlaceStatus">
            <div class="alert alert-info alert-dismissible fade show m-2" role="alert">
                保存しました！
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
        <div v-if="!saveMeetingPlaceStatus && saveMeetingPlaceStatus.length != 0">
            <div class="alert alert-info alert-dismissible fade show m-2" role="alert">
                解除しました！
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
        <div v-for="(mp, i) in meetingPlaces" class="media shadow m-2">
            <div class="media-body m-2">
                <div class="btn-toolbar">
                    <div class="mp_name">
                        <h5>@{{ mp.data.name }}</h5>
                    </div>
                    <div class="text-right ml-auto">
                        <div v-if="mp.isRegistered">
                            <button v-on:click="saveMeetingPlace(i)" type="button" class="btn btn-secondary">保存解除</button>
                        </div>
                        <div v-else>
                            <button v-on:click="saveMeetingPlace(i)" type="button" class="btn btn-success">保存する</button>
                        </div>
                    </div>
                </div>
                <p>@{{ prefecture[mp.data.prefecture_code-1] }}</p>
                <p style="white-space:pre-wrap; word-wrap:break-word;">@{{ mp.data.address }}</p>
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
            <div class="alert alert-secondary" role="alert">
                データがありません
            </div>
        </div>
    </div>
</div>
<div class="mt-5"></div>
<script src="{{ mix('js/meetingPlaceRegister.js') }}"></script>
@endsection
