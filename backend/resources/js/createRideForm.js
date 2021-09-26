import Vue from 'vue';
import jQuery, { data } from 'jquery'
global.jquery = jQuery
global.$ = jQuery
window.$ = window.jQuery = require('jquery')
window.axios = require('axios');

new Vue({
    el: '#app',
    data: {
        //受け取ったデータ
        meetingPlaces: {},
        rideRoutes: {},

        //エラー表示
        httpError: [],

        nameClass: 'form-control',
        nameErrComment: '',
        
        commentClass: 'form-control',
        commentErrComment: '',

        //ボタン操作
        isLoad: false,
        disableSubmitBtn: true,

        //入力補足
        intensityInfo: 'ゆるポタ(非競技勢向け)。体力や技術の向上を目的としないライドなど。',

        //フォーム入力
        selectedMeetingPlace: '',
        selectedRideRoute: '',
        name: '',
        time_appoint: '',
        intensity: 0,
        comment: '',
        publish_status: 0,
    },

    mounted() {
        this.getMeetingPlaces();
        this.getRideRoutes();
    },

    computed :{
        /**
         * 
         * @returns 
         */
        isValidSelectedMeetingPlace: function(){
            if(this.selectedMeetingPlace.length == 0){
                return false;

            }else{
                return true;
            }
        },

        /**
         * 
         * @returns 
         */
        isValidSelectedRideRoute: function(){
            if(this.selectedRideRoute.length == 0){
                return false;

            }else{
                return true;
            }
        },

        /**
         * 
         * @returns 
         */
        isValidName: function(){
            let status = false;
            let name = this.name;

            if(name.length == 0){
                status = 'min';

            }else if(name.length > 32){
                status = 'max';

            }else{
                status = true;
            }

            return status;
        },

        /**
         * 強度の説明を表示
         * 
         * @returns 
         */
        showIntensityInfo: function(){
            let intensity = this.intensity;
            
            if(intensity == 0){
                return 'ゆるポタ(非競技勢向け)。体力や技術の向上を目的としないライドなど。';

            }else if(intensity == 1){
                return 'ゆるポタ(競技勢向け)。体力や技術の向上を目的としないライドや回復走など。';

            }else if(intensity < 4){
                return '(L1-L2) LSDなどの低強度エンデュランストレーニング。もしくは技術を目的とした練習。';
            
            }else if(intensity < 5){
                return '(L3) テンポ走などのエンデュランストレーニングや1時間以上のペース走、峠TTなど。';

            }else if(intensity < 7){
                return '(L4) インターバル10-60分のFTP、筋持久力の向上を目的としたトレーニングや峠TT、ペース走など。';

            }else if(intensity < 8){
                return '(L5) インターバル3-8分のVO2maxの向上を目的としたトレーニングや登坂アタックなど。';

            }else if(intensity < 9){
                return '(L6) インターバル2分以下の無酸素能力の向上を目的としたトレーニングや登坂アタックなど。';
            
            }else if(intensity < 10){
                return '(L7) インターバル30秒以下のスプリント能力の向上を目的としたトレーニング。';

            }else{
                return 'レース走などレース強度での走行。時間に問わず、レース同様に力を出し切るトレーニング・練習。';

            }
        },

        /**
         * 
         * @returns 
         */
        isValidTime_appoint: function(){
            if(this.time_appoint.length == 0){
                return false;

            }else{
                return true;
            }
        },

        /**
         * 
         * @returns 
         */
         isValidComment: function(){
            let status = false;

            if(this.comment.length == 0){
                status = 'min';

            }else if(this.comment.length > 1024){
                status = 'max';
            }else{
                status = true;
            }

            return status;
        },

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
    },

    watch :{
        showIntensityInfo(){
            this.intensityInfo = this.showIntensityInfo;
        },

        isValidName(){
            switch(this.isValidName){
                case 'min':
                    this.nameClass = 'form-control is-invalid';
                    this.nameErrComment = '名前は必須です';
                    break;
                
                case 'max':
                    this.nameClass = 'form-control is-invalid';
                    this.nameErrComment = '名前は32文字以内で入力してください';
                    break;

                case true:
                    this.nameClass = 'form-control is-valid';
                    this.nameErrComment = '';
                    break;

                default:
                    this.nameClass = 'form-control';
                    this.nameErrComment = '';
                    break;
            }
        },

        isValidComment(){
            switch(this.isValidComment){
                case 'min':
                    this.commentClass = 'form-control is-invalid';
                    this.commentErrComment = 'ライドの説明は必須です';
                    break;

                case 'max':
                    this.commentClass = 'form-control is-invalid';
                    this.commentErrComment = 'ライドの説明は1024字以内で入力してください';
                    break;

                case true:
                    this.commentClass = 'form-control is-valid';
                    this.commentErrComment = '';
                    break;

                default:
                    this.commentClass = 'form-control';
                    this.commentErrComment = '';
                    break;
            }
        },

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
                this.httpError.push(error)

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
                this.httpError.push(error)

            }).then(res =>{
                self.rideRoutes = res.data;
            });

            self.isLoad = false;
        },



        /**
         * 
         * @param {*} url 
         * @param {*} data 
         */
        post: function(url, data) {
            const self = this;
      
            let axiosPost = axios.create({
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
              withCredentials: true
            });

            axiosPost.post(
                url,
                data
      
            ).catch(error => {
                console.log(error);
              
            }).then(res => {
              console.log(res);
            });
        },

        inputPublishStatus: function(val){
            this.publish_status = Number(val);
            this.$forceUpdate();
        }
    },
});