import Vue from 'vue';
import jQuery, { data } from 'jquery'
global.jquery = jQuery
global.$ = jQuery
window.$ = window.jQuery = require('jquery')
window.axios = require('axios');

new Vue({
    el: '#app',
    data: {
        //form-control
        form_control: [
            'form-control',            //false, 0
            'form-control is-valid',   //true, 1
            'form-control is-invalid', //min, 2
            'form-control is-invalid', //max, 3
        ],
        
        //受け取ったデータ
        meetingPlaces: {},
        rideRoutes: {},

        //エラー表示
        httpErrors: [],

        nameClass: 'form-control',
        nameErrComment: '',
        
        commentClass: 'form-control',
        commentErrComment: '',

        //ボタン操作
        isLoad: false,
        disableSubmitBtn: true,

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

        //フォーム入力
        selectedMeetingPlace: '',
        selectedRideRoute: '',
        date: '',
        time: '',
        name: '',
        intensity: 0,
        comment: '',
        publish_status: 0,

        time_appoint: '',
    },

    mounted() {
        this.getMeetingPlaces();
        this.getRideRoutes();
    },

    computed :{
        /**
         * selectedMeetingPlaceのバリデーションチェック
         * 
         * @returns bool
         */
        isValidSelectedMeetingPlace: function(){
            if(this.selectedMeetingPlace.length == 0){
                return false;

            }else{
                return true;
            }
        },

        /**
         * selectedRideRouteのバリデーションチェック
         * 
         * @returns bool
         */
        isValidSelectedRideRoute: function(){
            if(this.selectedRideRoute.length == 0){
                return false;

            }else{
                return true;
            }
        },

        /**
         * nameのバリデーションチェック
         * 
         * @returns int status
         */
        isValidName: function(){
            let status = false;
            let name = this.name;

            if(name.length == 0){
                status = 2;

            }else if(name.length > 32){
                status = 3;

            }else{
                status = true;
            }

            return status;
        },

        /**
         * time_appointのバリデーションチェック
         * 
         * @returns bool
         */
        isValidTime_appoint: function(){
            if(this.time_appoint.length == 0){
                return false;

            }else{
                return true;
            }
        },

        /**
         * commentのバリデーションチェック
         * 
         * @returns int status
         */
         isValidComment: function(){
            let status = false;

            if(this.comment.length == 0){
                status = 2;

            }else if(this.comment.length > 1024){
                status = 3;
            }else{
                status = true;
            }

            return status;
        },

        /**
         * すべてのフォームバリデートがtrueであることをチェック
         * 
         * @returns bool
         */
        isInValidForms: function(){
            if(
                this.isValidSelectedMeetingPlace == false
                || this.isValidSelectedRideRoute == false
                || this.isValidName != true
                || this.isValidTime_appoint == false
                || this.isValidComment != true 
            ){
                return true;
            }else{
                return false;
            }
        },

        /**
         * this.intensityから強度の説明のキーを返す
         * 
         * @returns string
         */
         showIntensityInfo: function(){
            let intensity = this.intensity;
            
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
         * dateとtimeをtime_appoint型にフォーマット
         * 
         * @returns string
         */
        formatToDateTime: function(){
            return this.date+' '+this.time;
        }
    },

    watch :{
        /**
         * formatToDateTimeを反映
         */
        formatToDateTime(){
            this.time_appoint = this.formatToDateTime;
        },

        /**
         * showIntensityInfoをビューに反映
         */
        showIntensityInfo(){
            this.intensityInfo = this.showIntensityInfo;
        },

        /**
         * this.isValidNameの値を元にバリデート内容をビューに反映
         */
        isValidName(){
            const form = '名前';
            const max = '32';
            let key = Number(this.isValidName);

            this.nameClass = this.form_control[key];
            this.nameErrComment = this.getValidationMessage(form, max, key);
        },

        /**
         * this.isValidCommentの内容を元にバリデート内容をビューに反映
         */
        isValidComment(){
            const form = 'ライドの説明';
            const max = '1024';
            let key = Number(this.isValidComment);

            this.commentClass = this.form_control[key];
            this.commentErrComment = this.getValidationMessage(form, max, key);
        },

        /**
         * isInValidFormsの値をthis.disableSubmitBtnに代入
         */
        isInValidForms(){
            this.disableSubmitBtn = this.isInValidForms;
        }

    },

    methods :{

        /**
         * 保存した集合場所を取得
         */
        getMeetingPlaces: function(){
            this.isLoad = true;
            const self = this;

            const url = 'api/get/savedMeetingPlaces';
            
            axios.get(url)
            .catch(error =>{
                console.log(error);
                this.httpErrors.push(error);
                return;

            }).then(res =>{
                self.meetingPlaces = res.data;
            });

            self.isLoad = false;
        },


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
            });

            self.isLoad = false;
        },

        /**
         * 押されたボタンの引数をthis.publish_statusに代入
         * 
         * @param {*} val 
         */
        inputPublishStatus: function(val){
            this.publish_status = Number(val);
            this.$forceUpdate();
        },
        

        getValidationMessage: function(form, max, key){
            const message = [
                '',                                       //false
                '',                                       //true 
                `${form}は必須です`,                       //min
                `${form}は${max}文字以内で入力してください`, //max
            ];

            return message[key];
        },

        /**
         * ライドの登録
         * 
         * @param {*} url 
         * @param {*} data 
         */
        submit: function() {
            const self = this;

            const url = 'api/post/createRide';

            let data = {
                "meeting_places_uuid":this.selectedMeetingPlace,
                "ride_routes_uuid":this.selectedRideRoute,
                "name":this.name,
                "time_appoint":this.time_appoint,
                "intensity":this.intensity,
                "comment":this.comment,
                "publish_status":this.publish_status
            }
      
            let axiosPost = axios.create({
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
              withCredentials: true
            });

            axiosPost.post(url, data)
            
            .catch(error => {
                console.log(error);
                this.httpErrors.push(error);
              
            }).then(res => {

              if(res.status){
                location.href = '/'; //[todo]ライド情報ページを作ったらそこに飛ばす
              }
            });
        },
    },
});