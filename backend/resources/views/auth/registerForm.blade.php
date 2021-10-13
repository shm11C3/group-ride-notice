@extends('layouts.layout')
@section('title','新規登録')
@section('content')
<div class="mt-10 mb-10">
    <h2>新規登録</h2>
</div>
@if ($errors->any())
  @foreach ($errors->all() as $error)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>{{ $error }}</strong>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endforeach
<x-alert type="danger" :session="session('login_error')"/>
<x-alert type="danger" :session="session('account_lock')"/>
<x-alert type="danger" :session="session('logout_success')"/>
@endif
<div id="app" v-cloak>
<div class="modal" id="privacyModal" tabindex="-1" role="dialog" aria-labelledby="privacyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="privacyModalLabel">利用規約・プライバシーポリシー</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div>
            <h4>第1条 個人情報の利用目的</h4>
            <br>
            <b>Bipokele</b>(以下当サービス)では、ユーザー登録、お問い合わせなどの際に、名前（ハンドルネーム）、メールアドレス等の個人情報をご登録いただく場合がございます。<br>
            <br>
            これらの個人情報は質問に対する回答や必要な情報を電子メールなどをでご連絡する場合に利用させていただくものであり、個人情報をご提供いただく際の目的以外では利用いたしません。<br>
            <br>
            <h4>第2条 個人情報の第三者への開示</h4>
            <br>
            当サービスでは、個人情報は適切に管理し、以下に該当する場合を除いて第三者に開示することはありません。<br>
            <br>
            ・本人のご了解がある場合<br>
            <br>
            ・法令等への協力のため、開示が必要となる場合<br>
            <br>
            個人情報の開示、訂正、追加、削除、利用停止<br>
            <br>
            ご本人からの個人データの開示、訂正、追加、削除、利用停止のご希望の場合には、ご本人であることを確認させていただいた上、速やかに対応させていただきます。<br>
            <br>
            <br>
            当サービスでは、スパム・荒らしへの対応として、投稿作成・ライド参加・ログインの際に使用されたIPアドレスを記録しています。<br>
            <br>
            これはスパム・荒らし・サイバー攻撃への対応以外にこのIPアドレスを使用することはありません。加えて、次の各号に掲げる内容を含む投稿作成・ライド参加は利用規約に反する行為に該当する場合承認せず、削除する事があります。<br>
            <br>
            <h4>第3条 当サービスへの投稿および、当サービス上でのライド参加について</h4>
            利用者は、本利用規約に同意頂いた上で、当サービスへの投稿および、当サービス上でのライド参加を利用できるものとします。<br>
            <br>
            当サービスへの投稿および、当サービス上でのライド参加は18歳以上及び日本国内の利用者に限定されます。
            <br>
            <br>
            ユーザーは，本サービスの利用にあたり，以下の行為をしてはなりません。
            <br>
            <br>・法令または公序良俗に違反する行為<br>
            <br>・犯罪行為に関連する行為<br>
            <br>・管理者，本サービスの他のユーザー，または第三者のサーバーまたはネットワークの機能を破壊したり，妨害したりする行為<br>
            <br>・管理者のサービスの運営を妨害するおそれのある行為<br>
            <br>・他のユーザーに関する個人情報等を収集または蓄積する行為<br>
            <br>・不正アクセスをし，またはこれを試みる行為<br>
            <br>・他のユーザーに成りすます行為<br>
            <br>・管理者のサービスに関連して，反社会的勢力に対して直接または間接に利益を供与する行為<br>
            <br>・管理者，本サービスの他のユーザーまたは第三者の知的財産権，肖像権，プライバシー，名誉その他の権利または利益を侵害する行為<br>
            <br>・以下の表現を含み，または含むと管理者が判断する内容を本サービス上に投稿し，または送信する行為<br>
            <br>・過度に暴力的な表現<br>
            <br>・露骨な性的表現<br>
            <br>・人種，国籍，信条，性別，社会的身分，門地等による差別につながる表現<br>
            <br>・自殺，自傷行為，薬物乱用を誘引または助長する表現<br>
            <br>・その他反社会的な内容を含み他人に不快感を与える表現<br>
            <br>・以下を目的とし，または目的とすると管理者が判断する行為<br>
            <br>・営業，宣伝，広告，勧誘，その他営利を目的とする行為（当社の認めたものを除きます。）<br>
            <br>・性行為やわいせつな行為を目的とする行為<br>
            <br>・面識のない異性との出会いや交際を目的とする行為<br>
            <br>・他のユーザーに対する嫌がらせや誹謗中傷を目的とする行為<br>
            <br>・管理者，本サービスの他のユーザー，または第三者に不利益，損害または不快感を与えることを目的とする行為<br>
            <br>・その他本サービスが予定している利用目的と異なる目的で本サービスを利用する行為<br>
            <br>・宗教活動または宗教団体への勧誘行為<br>
            <br>・その他，管理者が不適切と判断する行為<br>
            <br>
            <h4>第4条 免責事項</h4>
            <br>
            当サービスで掲載している画像の著作権・肖像権等は各権利所有者に帰属致します。権利を侵害する目的ではございません。<br>
            <br>
            当サービスからリンクやバナーなどによって他のサイトに移動された場合、移動先サイトで提供される情報、サービス等について一切の責任を負いません。<br>
            <br>
            当サービスに掲載された内容によって生じた損害等の一切の責任を負いかねますのでご了承ください。<br><br>
            <h4>第5条 お問い合わせ</h4>
            本ポリシーに関するお問い合わせは，下記の窓口までお願いいたします。<br>
            bipokele@gmail.com
            <br>
            <br>
            以上
            <br>
            2021年 10 月 14日　施行
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
        <div class="form-check">
          <label class="form-check-label">
            <input v-model="privacy" class="form-check-input" type="checkbox" value="1">
            同意する
          </label>
        </div>
      </div>
    </div>
  </div>
</div>
  <form class="login-form" method="POST" action="{{ route('register') }}">
      @csrf
      <div class="form-group">
          <label for="inputName">名前</label>
          <input v-model="name" type="text" name="name" v-bind:class="nameClass" placeholder="Name" value="{{ old('name') }}" autocomplete="name">
          <small id="nameInfo" class="form-text text-muted">公序良俗に反する内容は入力しないでください</small>
      </div>
      <div class="form-group has-danger">
        <label for="inputEmail1">メールアドレス</label>
        <input v-model="email" type="email" name="email" v-bind:class="emailClass" id="inputEmail1" aria-describedby="emailHelp" placeholder="Email" value="{{ old('email') }}" autocomplete="email">
        <small id="emailHelp" class="form-text text-muted">ここで入力したメールアドレスは公開されません</small>
      </div>
      <div class="form-group has-danger">
        <label for="inputPassword1">パスワード</label>
        <input v-model="password" type="password" name="password" v-bind:class="passwordClass" id="inputPassword1" placeholder="Password" value="{{ old('password') }}" autocomplete="new-password">
        <div class="invalid-feedback">@{{ passwordError }}</div>
      </div>
      <div class="form-group has-danger">
          <label for="inputPassword1">パスワード（確認）</label>
          <input v-model="password_confirmation" type="password" v-bind:class="confirmClass" id="inputPassword-confirm" placeholder="Password (confirm)" autocomplete="off">
      </div>
      <div class="form-group">
        <label for="select1">お住まいの都道府県</label>
        <select name="prefecture_code" class="form-control" id="select1">
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
      <fieldset class="form-group">
        <div class="form-check">
          <label class="form-check-label">
            <input name="remember" class="form-check-input" type="checkbox" value="1" checked="">
            アカウントを記憶
          </label>
        </div>
      </fieldset>
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#privacyModal">
        プライバシーポリシーに同意
      </button>
      <div class="text-right">
        <a class="btn btn-secondary" href="{{ route('showLogin') }}">登録済みの方はこちら</a>
        <button v-bind:disabled="!submitStatus" type="submit" class="btn btn-success">送信</button>
      </div>
  </form>
</div>
<script src="{{ mix('js/registerForm.js') }}"></script>
@endsection