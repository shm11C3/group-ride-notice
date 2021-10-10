import Vue from 'vue';
import jQuery, { data } from 'jquery'
global.jquery = jQuery
global.$ = jQuery
window.$ = window.jQuery = require('jquery')
window.axios = require('axios');

new Vue({
    el: '#app',
    data: {
        //固定値
        prefecture:["北海道","青森県","岩手県","宮城県","秋田県","山形県","福島県",
                    "茨城県","栃木県","群馬県","埼玉県","千葉県","東京都","神奈川県",
                    "新潟県","富山県","石川県","福井県","山梨県","長野県","岐阜県",
                    "静岡県","愛知県","三重県","滋賀県","京都府","大阪府","兵庫県",
                    "奈良県","和歌山県","鳥取県","島根県","岡山県","広島県","山口県",
                    "徳島県","香川県","愛媛県","高知県","福岡県","佐賀県","長崎県",
                    "熊本県","大分県","宮崎県","鹿児島県","沖縄県"],
                    
        publish_status_arr: ['公開', '限定公開', '非公開'],

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

        publish_icon: [
            'fa-eye', 'fa-lock-open', 'fa-eye-slash'
        ],

        intensityInfo: '',



        //状態
        isLoad: false,
        participateModal: false,
        pt_isPush: false,

        mp_infoStatus: false,
        rr_infoStatus: false,


        //HTTP Error
        httpErrors: [],
        authError: false,

        //受信データ        
        ride: '',
        time: '',
        weathers: '',

        //送信データ
        participateIndex: '',
        participateComment: '',
    },

    mounted(){
        const param = this.getQueryParam();
        this.getRide(param);        
    },

    methods: {
        showLine: function(){
            window.LineIt.loadButton()
        },

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

            let url = `api/get/ride/${request_uuid}`;

            axios.get(url)
            .catch(error =>{
                console.log(error);
                this.httpErrors.push(error);
                this.isLoad = false;

            }).then(res =>{
                let data = res.data;
                this.authError = !Boolean(data.length);
                this.ride = data[0];
                this.time = this.ride.time_appoint.substring(5,7)+'月'+this.ride.time_appoint.substring(8,10)+'日'+this.ride.time_appoint.substring(10,16);

                this.isLoad = false;

                this.intensityInfo = this.showIntensityInfo(this.ride.intensity);
            });
        },

        popUpTweetWindow() {
            const snsText = `ライド開催！%0a${this.ride.ride_name}@${this.ride.address}%0a${this.time}%0a`;

            const url = `https://twitter.com/intent/tweet?text=${snsText}&url=${location}`;
            const option = 'status=1,width=818,height=400,top=100,left=100';

            window.open(url, 'twitter', option)    
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

        /**
         * 詳細表示のコントロール
         */
        mp_showInfo: function(){
            this.mp_infoStatus = !this.mp_infoStatus;

            if(!this.weathers.length){
                this.getWether();
            }
        },
        rr_showInfo: function(){
            this.rr_infoStatus = !this.rr_infoStatus;
        },

        getWether: function(){
            const url = `https://www.jma.go.jp/bosai/forecast/data/forecast/${this.ride.prefecture_code}0000.json`;

            

            axios.get(url)
            .catch(error =>{
                console.log(error);
                this.httpErrors.push(error);
        
            }).then(res =>{
                this.weathers = res.data[0].timeSeries[0].areas;
            });
        },
        
        
        openParticipateModal: function(){
            this.participateComment = 'よろしくお願いします。';
            this.participateModal = true;
    
            const options = {};

            $('#participateModal').modal(options);
        },
    
        closeParticipateModal: function(){
            this.participateComment = '';
            this.participateModal = false;

            $('#participateModal').modal('hide');
            $('#cancelParticipateModal').modal('hide');
        },
    
        participation: function(){
            this.pt_isPush = true;
    
            const url = 'api/post/participation';
    
            let data = {
                "ride_uuid" : this.ride.uuid,
                "comment" : this.participateComment
            }
    
            let axiosPost = axios.create({
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                withCredentials: true
            });
    
            axiosPost.post(url, data)
            .catch(error => {
                console.log(error);
    
                this.httpErrors.push(error);
                
                this.pt_isPush = false;
    
            }).then(res => {

                this.getRide(this.getQueryParam());
    
                this.closeParticipateModal();    
                this.pt_isPush = false;
            });
        },
    
        cancelParticipation: function(){
            this.pt_isPush = true;
    
            const url = 'api/post/participation/delete';
    
            let data = {
                "ride_uuid" : this.ride.uuid,
            }
    
            let axiosPost = axios.create({
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                withCredentials: true
            });
    
            axiosPost.post(url, data)
            .catch(error => {
                console.log(error);
    
                this.httpErrors.push(error);
                
                this.pt_isPush = false;
    
            }).then(res => {
    
                this.getRide(this.getQueryParam());
    
                this.closeParticipateModal();
                this.pt_isPush = false;
            });
        },

    },
});