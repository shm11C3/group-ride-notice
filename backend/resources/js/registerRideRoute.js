import Vue from 'vue';

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
    },

    mounted(){
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

            this.callRideRoutes();
        },
        /**
         * 2ページ以降のデータを取得する処理を呼び出す
         */
        callAddLoad: function (){
            this.isLoad = true;

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
         * @param {*} i
         */
        saveRideRoute: function(i){
            this.rideRoutes[i].isRegistered = !this.rideRoutes[i].isRegistered;
            this.saveRideRouteStatus = '';

            const ride_route_uuid = this.rideRoutes[i].data.uuid;
            const url = '../api/post/registerRideRoute';

            const data = {
                "ride_route_uuid" : ride_route_uuid
            }

            fetch(url,
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

                })
                .catch(e => {
                    console.error(e);
                });
        }
    }
})


