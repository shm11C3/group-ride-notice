<div class="bs-component sticky-top">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <a class="navbar-brand" href="/"><span class="text-accent">B</span>ipokele</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarColor01">
        <ul class="navbar-nav mr-auto">
          <li  @if(request()->path() === '/') class="nav-item active" @else class="nav-item" @endif>
            <a class="nav-link" href="{{ route('showHome') }}">ホーム<span class="sr-only">(current)</span></a>
          </li>
          <li @if(request()->path() === 'my-rides') class="nav-item active" @else class="nav-item" @endif>
            <a class="nav-link" href="{{ route('showMyRides') }}">参加予定のライド<span class="sr-only">(current)</span></a>
          </li>
          <li @if(request()->path() === 'search') class="nav-item active" @else class="nav-item" @endif>
            <a class="nav-link" href="{{ route('showSearch') }}">検索する<span class="sr-only">(current)</span></a>
          </li>
          <li @if(request()->path() === 'create-ride' || request()->path() === 'meeting-place/register' || request()->path() === 'ride-route/register') class="nav-item dropdown active" @else  class="nav-item dropdown" @endif>
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              ライド管理
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="{{ route('createRideForm') }}">ライドを作成</a>
              <a class="dropdown-item" href="{{ route('showRegisterRideRouteForm') }}">コースを登録</a>
              <a class="dropdown-item" href="{{ route('showMeetingPlaceRegisterForm') }}">集合場所を登録</a>
            </div>
          </li>
          @auth
          <li @if(request()->path() === 'user/'.Auth::user()->uuid || request()->path() === 'user/config' || request()->path() === 'dashboard') class="nav-item dropdown active" @else class="nav-item dropdown" @endif>
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">マイプロフィール<span class="sr-only">(current)</span></a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" class="nav-link" href="{{ route('showUser', Auth::user()->uuid) }}">マイプロフィール</a>
              <a class="dropdown-item" href="{{ route('showConfig') }}">設定</a>
              <a class="dropdown-item" class="nav-link" href="{{ route('showDashboard') }}">登録情報</a>
            </div>
          </li>
          @endauth
        </ul>
        <form class="form-inline my-2 my-lg-0">
          @auth
          <a href="{{ route('showDashboard') }}">
            <img class="bd-placeholder-img user_profile_img_xs" src="{{ Auth::user()->userProfile->user_profile_img_path ?? asset('img/user_profiles/default_profile_75.png') }}">
          </a>
          @endauth
          @guest
          <a class="btn btn-success my-2 my-sm-0" href="{{ route('showLogin') }}">ログイン</a>
          @endguest
        </form>
      </div>
    </nav>
  </div>
