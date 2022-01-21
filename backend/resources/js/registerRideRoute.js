import Vue from 'vue';

new Vue({
    el: '#app',
    data: {
        ride_route_page: 0,
        rideRoutes: [],

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
                this.addLoad();
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
         */
        initialLoad: function(){
            this.rideRoutes = [];
            this.ride_route_page = 1;

            this.fetchRideRoutes();
        },
        addLoad: function (){
            this.ride_route_page++;
            this.fetchRideRoutes();
        },

        /**
         * ライドルートリストを取得
         */
        fetchRideRoutes: function(){
            this.isLoad = true;

            let uri ='';
            if(this.lap_status_request == 3){
                uri = `../api/strava/get/route/${this.ride_route_page}`;
            }else{
                uri = `../api/get/ride-routes/${this.lap_status_request}?page=${this.ride_route_page}`;
            }

            fetch(uri)
            .then(response => {
                return response.json();

            }).then(data => {
                this.isLoad = false;

                if(data.auth_uuid){
                    this.auth_uuid = data.auth_uuid;

                    data.ride_routes.forEach(element => {
                        this.rideRoutes.push(element);
                    });

                    this.resIsExist = Boolean(data.next_page_url);
                    this.$forceUpdate();

                }else if(data[0]['data'].strava_route_id){
                    data.forEach(element => {
                        this.rideRoutes.push(element);
                    });

                    this.resIsExist = Boolean(data[0]);
                    this.$forceUpdate();

                }else{
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
                    this.rideRoutes[i].isRegistered = res.status;


                })
                .catch(e => {
                    console.error(e);
                });
        }
    }
})
