@extends('layouts.layout')
@section('title', '検索')
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
    <div class="form-group">
        <input type="search" v-model="searchWord"  @keypress.prevent.enter.exact="enable_submit" @keyup.prevent.enter.exact="submit" class="form-control" aria-describedby="searchWordHelp" placeholder="検索">
        <small id="searchWordHelp" class="form-text text-muted">検索したいワードを入力してください</small>
    </div>
    <div v-if="isLoad">
        <div class="d-flex justify-content-center">
            <div class="spinner-grow text-success mb-30" style="width: 3rem; height: 3rem; margin-top: 200px;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
    <div v-else>
        <div v-if="dataIsExist">
            <div class="alert alert-secondary" role="alert">
                検索結果はありません
            </div>
        </div>
    </div>
</div>
<script src="{{ mix('js/search.js') }}"></script>
@endsection