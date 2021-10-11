<div class="bs-component sticky-top">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <a class="navbar-brand" href="/"><span class="text-accent">B</span>ipokele</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarColor01">
        <ul class="navbar-nav mr-auto">
          @if(url()->current() === 'http://www.localhost')
          <li class="nav-item active">
            <a class="nav-link" href="/">ホーム<span class="sr-only">(current)</span></a>
          </li>
          @else
          <li class="nav-item">
            <a class="nav-link" href="/">ホーム<span class="sr-only">(current)</span></a>
          </li>
          @endif
          <li class="nav-item">
            <a class="nav-link" href="#">参加予定のライド<span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              ライドを探す
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="#">近日開催予定のライド</a>
              <a class="dropdown-item" href="#"></a>
              <a class="dropdown-item" href="#">ライド検索</a>
            </div>
          </li>
          @if(url()->current() === 'http://www.localhost/create-ride')
          <li class="nav-item dropdown active">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              ライド管理
            </a>
          @else
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              ライド管理
            </a>
          @endif
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="{{ route('createRideForm') }}">ライドを作成</a>
              <a class="dropdown-item" href="#">コースを登録</a>
              <a class="dropdown-item" href="#">集合場所を登録</a>
            </div>
          </li>
          @auth
          @if(url()->current() === 'http://www.localhost/dashboard')
          <li class="nav-item dropdown active">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">マイプロフィール<span class="sr-only">(current)</span></a>
          @else
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">マイプロフィール<span class="sr-only">(current)</span></a>
          @endif
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
          <a class="btn btn-secondary my-2 my-sm-0" href="{{ route('showDashboard') }}">{{ Auth::user()->name }}</a>    
          @endauth
          @guest
          <a class="btn btn-success my-2 my-sm-0" href="{{ route('showLogin') }}">ログイン</a>
          @endguest
        </form>
      </div>
    </nav>
  </div>