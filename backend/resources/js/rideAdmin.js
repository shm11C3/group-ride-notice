import Vue from 'vue';
import jQuery, { data } from 'jquery'
global.jquery = jQuery
global.$ = jQuery
window.$ = window.jQuery = require('jquery')
window.axios = require('axios');

new Vue({
    el: '#app',
    data: {
        //パラメーター
        request_uuid: '',


        //受信データ
        ride: '',

        meetingPlaces: {},
        rideRoutes: {},

        nameClass: 'form-control',
        nameErrComment: '',
        
        commentClass: 'form-control',
        commentErrComment: '',

        //固定値
        prefecture:["北海道","青森県","岩手県","宮城県","秋田県","山形県","福島県",
                    "茨城県","栃木県","群馬県","埼玉県","千葉県","東京都","神奈川県",
                    "新潟県","富山県","石川県","福井県","山梨県","長野県","岐阜県",
                    "静岡県","愛知県","三重県","滋賀県","京都府","大阪府","兵庫県",
                    "奈良県","和歌山県","鳥取県","島根県","岡山県","広島県","山口県",
                    "徳島県","香川県","愛媛県","高知県","福岡県","佐賀県","長崎県",
                    "熊本県","大分県","宮崎県","鹿児島県","沖縄県"],

        publish_status_arr: ['公開', '限定公開', '非公開'],

        
        //送信データ
        selectedRideRoute: '',
        name: '',
        intensity: 0,
        num_of_laps: 0,
        comment: '',

        selectedRideRouteKey: '',


        isPush: false,
        isLoad: false,
        updateForm: false,
        update: false,
        disableSubmitBtn: true,

        //サーバーエラー
        httpErrors: [],
        authError: false,

        selectedLap_status: false,

         //入力補足
        intensityStyle: [
            'intst-lowest',
            'intst-lowest',
            'intst-endurance',
            'intst-tempo',
            'intst-LactateThreshold',
            'intst-VO2Max',
            'intst-AnaerobicCapacity',
            'intst-NeuromuscularPower',
            'intst-highest'
        ],

        intensityComment: [
            'ゆるポタ(非競技勢向け)。体力や技術の向上を目的としないライドなど。',
            'ゆるポタ(競技勢向け)。体力や技術の向上を目的としないライドや回復走など。',
            '(L1-L2) LSDなどの低強度エンデュランストレーニング。もしくは技術を目的とした練習。',
            '(L3) テンポ走などのエンデュランストレーニングや1時間以上のペース走、峠TTなど。',
            '(L4) インターバル10-60分のFTP、筋持久力の向上を目的としたトレーニングや峠TT、ペース走など。',
            '(L5) インターバル3-8分のVO2maxの向上を目的としたトレーニングや登坂アタックなど。',
            '(L6) インターバル2分以下の無酸素能力の向上を目的としたトレーニングや登坂アタックなど。',
            '(L7) インターバル30秒以下のスプリント能力の向上を目的としたトレーニング。',
            'レース走などレース強度での走行。時間に問わず、レース同様に力を出し切るトレーニング・練習。',
        ],

        intensityInfo: '',
    },

    mounted() {        
        const param = this.getQueryParam();
        this.getRide(param);
    },

    computed: {
        /**
         * すべてのフォームに値が入力されているかチェック
         */
        isValidForms: function(){
            let formLength
                 = this.selectedRideRoute.length
                 * this.selectedRideRoute.length
                 * this.name.length
                 * this.comment.length;

        
            let intst = this.intensity;
            let laps = this.num_of_laps;
        
            if(intst >= 0 && intst <= 10 && formLength != 0 && laps>=0 && laps<=255){
                return true;

            }else{
                return false;
            }
        }
    },

    watch: {
        /**
         * RideRouteKey変更時に関係する値を反映させる
         */
        selectedRideRouteKey(){
            let rideRoute = this.rideRoutes.data[this.selectedRideRouteKey];
            
            this.selectedRideRoute = rideRoute.uuid;
            this.selectedLap_status = rideRoute.lap_status
        },

        /**
         * 強度変更時にshowIntensityInfoを呼び出す
         */
        intensity(){
            this.intensityInfo = this.showIntensityInfo(this.intensity);
        },

        isValidForms(){
            this.disableSubmitBtn = !this.isValidForms;
        }
    },

    methods: {
        /**
         * パラメータからライドのUUIDを取得
         */
        getQueryParam: function(){
            const param = location.search;

            return param.substr(6,36)
        },

        /**
         * ライドの取得
         */
        getRide: function(request_uuid){
            this.isLoad = true;
            const self = this;

            let url = `/api/get/my-ride/${request_uuid}`;

            axios.get(url)
            .catch(error =>{
                console.log(error);
                this.httpErrors.push(error);
                this.isLoad = false;

            }).then(res =>{
                let data = res.data;
                this.authError = !Boolean(data.length);
                this.ride = data;

                this.isLoad = false;
                this.intensityInfo = this.showIntensityInfo(this.ride[0].intensity);
            });
        },

        /**
         * this.intensityから強度の説明のキーを返す
         * 
         * @returns string
         */
         showIntensityInfo: function(intensity){        
            if(intensity == 0){
                return 0;

            }else if(intensity == 1){
                return 1;

            }else if(intensity < 4){
                return 2;
            
            }else if(intensity < 5){
                return 3;

            }else if(intensity < 7){
                return 4;

            }else if(intensity < 8){
                return 5;

            }else if(intensity < 9){
                return 6;
            
            }else if(intensity < 10){
                return 7;

            }else{
                return 8;
            }
        },



        /*********************************************
         * ライド更新処理
         */

        /**
         * 保存した集合場所を取得
         */
        getRideRoutes: function(){
            this.isLoad = true;
            const self = this;

            const url = 'api/get/savedRideRoutes';

            axios.get(url)
            .catch(error =>{
                console.log(error);
                this.httpErrors.push(error);

            }).then(res =>{
                self.rideRoutes = res.data;

                let route_uuid = self.ride[0].ride_routes_uuid;
                let ride_route_index = self.rideRoutes.data.findIndex((el) => el.uuid === route_uuid);
                
                this.selectedRideRouteKey = ride_route_index;
            });

            self.isLoad = false;
        },


        /**
         * ライド更新フォーム表示時の処理
         */
        openUpdate: function() {
            this.updateForm = true;
            this.update = false;
            this.getRideRoutes();

            const ride = this.ride[0];

            this.name = ride.ride_name;
            this.comment = ride.ride_comment;
            this.num_of_laps = ride.num_of_laps;
            this.intensity = ride.intensity;
        },

        /**
         * ライド更新フォーム非表示
         */
        closeUpdate:function() {
            this.updateForm = false;

            this.name = '';
            this.comment = '';
        },

        /**
         * ライドの登録
         */
        submit: function() {
            const self = this;

            this.isPush = true;

            const url = 'api/post/updateRide';

            let data = {
                "uuid":this.ride[0].uuid,
                "ride_routes_uuid":this.selectedRideRoute,
                "name":this.name,
                "intensity":this.intensity,
                "num_of_laps":this.num_of_laps,
                "comment":this.comment,
            }
      
            let axiosPost = axios.create({
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
              withCredentials: true
            });

            axiosPost.post(url, data)
            
            .catch(error => {
                console.log(error);
                this.httpErrors.push(error);

                this.isPush = false;
              
            }).then(res => {

              if(res.status){
                this.updateForm = false;
                this.update = true;
                this. getRide(this.getQueryParam());
              }

              this.isPush = false;
            });
        },
    }
});