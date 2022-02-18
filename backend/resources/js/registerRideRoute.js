import Vue from 'vue';
import LoadImage from './methods/loadImage';

new Vue({
    el: '#app',
    data: {
        ride_route_page: 0,
        rideRoutes: [],

        strava_routes:[], // 一度取得したルート（APIリクエスト削減のため）
        strava_route_page: 1, // 上データ取得時のペジネーション

        isLoad: false,

        lap_status_request: 1,
        resIsExist: false,
        auth_uuid: '',
        saveRideRouteStatus: '',

        lapStatus: ['active', ''],

        isImgLoaded: [],
        opacity: [],

        isPush: false,
    },

    mounted(){
        this.loadImage = new LoadImage();

        this.initialLoad();

        this.observer = new IntersectionObserver((entries) => {
            const entry = entries[0];

            if (entry && entry.isIntersecting && this.resIsExist) {
                this.callAddLoad();
            }
        });

        const observe_element = this.$refs.observe_element;
        this.observer.observe(observe_element);
    },

    watch: {
        lap_status_request(){
            this.initialLoad();
        }
    },

    methods: {
        /**
         * ルート取得の呼び出し
         * ページ読み込み時に呼び出し
         */
        initialLoad: function(){
            this.rideRoutes = [];
            this.ride_route_page = 1;
            this.isImgLoaded = [];

            this.callRideRoutes();
        },
        /**
         * 2ページ以降のデータを取得する処理を呼び出す
         */
        callAddLoad: function (){
            if(this.lap_status_request == 3){
                // STRAVA追加ルート取得時
                this.strava_route_page++;
                this.fetchRideRoutes(`../api/strava/get/route/${this.strava_route_page}`);
            }else{
                // STRAVA以外追加ルート取得時
                this.ride_route_page++;
                this.callRideRoutes();
            }
        },

        /**
         * ライドルートリストを取得
         */
        callRideRoutes: function(){
            if(this.lap_status_request == 3){
                // STRAVAから取得のラジオボタン押下時
                if(this.strava_routes.length){
                    // 一度取得している場合
                    this.rideRoutes = this.strava_routes;

                    this.resIsExist = true;
                    this.isLoad = false;

                }else{
                    // 初回取得時
                    this.fetchRideRoutes('../api/strava/get/route/1')
                }
            }else{
                this.fetchRideRoutes(`../api/get/ride-routes/${this.lap_status_request}?page=${this.ride_route_page}`);
            }
        },

        /**
         * ルートのデータ一覧をJSONでフェッチ
         *
         * @fetch backend/app/Http/Controllers/Api/Ride/RideRouteController.getAllRideRoutes()
         * OR
         * @fetch backend/app/Http/Controllers/Api/StravaController.getUserRoute()
         *
         * @param {string} uri
         */
        fetchRideRoutes: function(uri){
            this.isLoad = true;

            fetch(uri)
            .then(response => {
                return response.json();

            }).then(data => {
                this.isLoad = false;

                if(data.auth_uuid){
                    // レスポンスが`ride_routes`テーブルから取得したルートの場合
                    this.auth_uuid = data.auth_uuid;

                    data.ride_routes.forEach(element => {
                        this.rideRoutes.push(element);
                    });

                    this.resIsExist = Boolean(data.next_page_url);
                    this.$forceUpdate();

                }else if(data[0]){
                    // レスポンスがSTRAVA APIから取得したルートの場合
                    data.forEach(element => {
                        this.rideRoutes.push(element);
                        this.isImgLoaded.push(false);
                        this.opacity.push(this.loadImage.default_ride_opacity);
                    });

                    this.strava_routes = this.rideRoutes; // stravaデータをクライアント側で一時保存

                    this.resIsExist = Boolean(data[0]);
                    this.$forceUpdate();

                }else{
                    // レスポンスが存在しない場合
                    this.resIsExist = false;
                }

            }).catch(error => {
                this.isLoad = false;
                console.log(error);
            });
        },

        changeLapStatus: function(request){
            this.lap_status_request = request;

            this.initialLoad();
        },

        /**
         * ride_routeを保存
         *
         * @param {int} i
         */
        saveRideRoute: function(i){
            this.isPush = true;
            this.saveRideRouteStatus = '';

            const ride_route_uuid = this.rideRoutes[i].data.uuid;

            let uri = '';
            let data = {};

            if(this.lap_status_request == 3 && !ride_route_uuid && this.rideRoutes[i].isRegistered == false){
                // ルートが DBに登録されていない場合
                const rideRoutes = this.rideRoutes[i].data;
                uri = '../api/post/rideRoute';
                data = {
                    "name"            : rideRoutes.name,
                    "elevation"       : rideRoutes.elevation,
                    "distance"        : rideRoutes.distance,
                    "lap_status"      : false,
                    "comment"         : rideRoutes.comment || 'STRAVAからインポート', // todo コメントがnullだと通らないので要対策
                    "publish_status"  : rideRoutes.publish_status,
                    "save_status"     : true,
                    "map_img_uri"     : rideRoutes.map_img_uri,
                    "strava_route_id" : rideRoutes.strava_route_id,
                }
            }else{
                // すでに存在するルートの保存・解除

                if(!ride_route_uuid){
                    // 予期しないエラーでride_route_uuidが存在しなかった場合
                    this.isPush = false;
                    return;
                }
                uri = '../api/post/registerRideRoute';
                data = {"ride_route_uuid" : ride_route_uuid};
            }

            this.rideRoutes[i].isRegistered = !this.rideRoutes[i].isRegistered; // fetchの前に送信ボタンの見た目だけ変えておく

            fetch(uri,
                {
                    method: 'POST',
                    headers: {
                        "Content-Type": "application/json",
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        withCredentials: true
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    return response.json();
                })
                .then(res => {
                    this.saveRideRouteStatus = res.status;

                    // ルートのUUIDが存在しない場合値を代入
                    if(!this.rideRoutes[i].data.uuid && res.uuid){
                        this.rideRoutes[i].data.uuid = res.uuid;
                        this.$forceUpdate();
                    }

                    this.rideRoutes[i].isRegistered = res.status;

                    this.isPush = false;
                })
                .catch(e => {
                    this.rideRoutes[i].isRegistered = !this.rideRoutes[i].isRegistered;
                    this.isPush = false;
                    console.error(e);
                });
        },

        load_img: function(i){
            this.isImgLoaded[i] = true;
            this.opacity[i] = '';
            this.$forceUpdate();
        }
    }
})


