@extends('layouts.layout')
@section('title','ライド作成')
@section('content')
<h2>ライドを作成</h2>
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
  <div class="form-group">
    <label for="name">ライド名</label>
    <input v-model="name" type="text" name="name" v-bind:class="nameClass" placeholder="ライド名">
    <div class="invalid-feedback">@{{ nameErrComment }}</div>
  </div>

  <div class="form-group">
    <label for="meetingPlace">ルートを選択</label>
    <div v-if="rideRoutes.data">
      <select class="form-control" id="select1" v-model="selectedRideRoute">
        <option value="">選択してください</option>
        <option v-for="(rideRoute, index) in rideRoutes.data" v-bind:value="rideRoute.uuid">
          @{{ rideRoute.name }}
        </option>
      </select>
    </div>
    <div v-else class="text-center">
        <div class="spinner-grow spinner-grow-sm" role="status">
          <span class="sr-only">Loading...</span>
        </div>
    </div>
  </div>

  <div class="form-group">
    <label for="meetingPlace">集合場所を選択</label>
    <div v-if="meetingPlaces.data">
      <select class="form-control" id="select1" v-model="selectedMeetingPlace">
        <option value="">選択してください</option>
        <option v-for="(meetingPlace, index) in meetingPlaces.data" v-bind:value="meetingPlace.uuid">
          @{{ meetingPlace.name }}
        </option>
      </select>
    </div>
    <div v-else class="text-center">
      <div class="spinner-grow spinner-grow-sm" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col">
      <div class="form-group">
        <label for="date">開催日</label>
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

  <div class="form-group">
    <label for="exampleFormControlTextarea1">ライドの説明</label>
    <textarea v-bind:class="commentClass" id="exampleFormControlTextarea1" rows="3" v-model="comment"></textarea>
    <div class="invalid-feedback">@{{ commentErrComment }}</div>
  </div>

  <div class="row">
    <div class="form-group col">
      <label for="intensityControlRange">
        ライドの強度(ペース)<br>
        ※トレーニングの場合はメインセットに合わせて選択してください
      </label>
      <input type="range" class="intensity-range" min="0" max="10" value="0" id="intensityControlRange" v-model="intensity">
    </div>
    <div class="col">
      <div class="alert alert-secondary" role="alert">
        <span class="intst-display">強度：<span v-bind:class="intensityStyle[showIntensityInfo]">@{{ intensity }}</span></span><br>
        @{{ intensityComment[showIntensityInfo] }}
      </div>
    </div>
  </div>

  <div class="btn-group btn-group-toggle status-radio-group mt-2" data-toggle="buttons">
    <label class="btn btn-outline-secondary status-radio">
        <input type="radio" name="publish_status" autocomplete="off" v-on:click="inputPublishStatus('0')" checked>公開
    </label>
    <label class="btn btn-outline-secondary status-radio">
        <input type="radio" name="publish_status" autocomplete="off" v-on:click="inputPublishStatus('1')">限定公開
    </label>
    <label class="btn btn-outline-secondary status-radio">
        <input type="radio" name="publish_status" autocomplete="off" v-on:click="inputPublishStatus('2')">非公開
    </label>
  </div>

  <div class="text-right mt-2 mr-5 mb-5">
    <button type="submit" class="btn btn-success" v-on:click="submit" v-bind:disabled="disableSubmitBtn">送信</button>
  </div>

</div>
<script src="{{ mix('js/createRideForm.js') }}"></script>
@endsection