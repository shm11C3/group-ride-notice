@extends('layouts.layout')
@section('title','ホーム')
@section('content')
<div id="app">
@auth
<!--ライド参加時のモーダル-->
<div class="model-group">

</div>
@endauth
<!--次の参加予定のライドを表示-->
<div class="ride-schedule-group">
    @auth
    <h2>次に参加予定のライド</h2>

    @endauth
    @guest
    <h3>次に参加予定のライド</h3>
    <div class="alert alert-info mt-2" role="alert">
        <p>ライドの参加には<a href="{{ route('showLogin') }}" class="alert-link">ログイン</a>が必須です！</p>
    </div>
    @endguest
</div>
<!--一覧系表示-->
<div class="rides-group mt-5">
    <h3>ライド一覧</h3>
    <div class="row">
        <div class="col-12 col-lg-3 form-group">
            <label for="time_appoint">開催日時</label>
            <select v-on:change="input_time_appoint" v-bind:disabled="isLoad" class="form-control">
                <option value="0">すべて表示</option>
                <option value="1">本日開催</option>
                <option value="2">明日開催</option>
                <option value="3">1週間以内開催</option>
                <option value="4">1ヶ月以内開催</option>
                <option value="5">開催終了</option>
            </select>
        </div>
        <div class="col-12 col-lg-3 form-group">
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
        <div class="col-12 col-lg-3 form-group">
            <label for="intensity">強度</label>
            <select v-on:change="input_intensity" v-bind:disabled="isLoad" class="form-control">
                <option value="0">すべて表示</option>
                <option value="1">かなり緩め</option>
                <option value="2">低強度</option>
                <option value="3">中強度</option>
                <option value="4">高強度</option>
            </select>
        </div>
        <div class="col-12 col-lg-3">
        </div>
    </div>
    <div class="rides">
        <div v-if="isLoad">
            <div class="d-flex justify-content-center">
                <div class="spinner-grow text-success" style="width: 3rem; height: 3rem; margin-top: 200px;" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>
        <div v-else>
            <div >
                <div v-for="(ride, index) in rides" class="media ride shadow mt-3">
                    <svg class="bd-placeholder-img align-self-start mr-3" width="64" height="64" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Placeholder: 64x64"><title>Placeholder</title><rect width="100%" height="100%" fill="#868e96"/><text x="50%" y="50%" fill="#dee2e6" dy=".3em">64x64</text></svg>
                    <div class="media-body">
                      <h5 class="mt-0 bg-primary text-white">@{{ride.ride_name}}</h5>
                        <p>@{{ ride.rr_name }}</p>
                        <div v-if="ride.num_of_laps > 0">
                            <p>@{{ ride.num_of_laps }}周</p>
                        </div>
                        <p>@{{ ride.time_appoint.substring(5,16) }}に@{{ ride.mp_name }}集合</p>
                        <div v-if="ride.num_of_laps == 0">
                            <p>@{{ ride.distance }}km</p>
                            <p>@{{ ride.elevation }}m up</p>
                        </div>
                        <div v-else>
                            <p>@{{ ride.num_of_laps*ride.distance }}km</p>
                            <p>@{{ ride.num_of_laps*ride.elevation }}m up</p>
                        </div>
                        <p>強度：@{{ ride.intensity }}</p>
                    </div>
                  </div>
            </div>
        </div>
    </div>
</div>
</div>
<script src="{{ mix('js/home.js') }}"></script>
@endsection