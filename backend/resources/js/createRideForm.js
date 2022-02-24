import Vue from 'vue';
import jQuery, { data } from 'jquery'
import {intensityStyle, intensityComment} from './constants/constant'
import {commentRule, nameRule, lapRule} from './constants/ride'
import OutputError from './methods/outputError'
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
        mp_httpErrors: [],
        rr_httpErrors: [],

        nameClass: 'form-control',
        nameErrComment: '',

        commentClass: 'form-control',
        commentErrComment: '',

        //ボタン操作
        isLoad: false,
        disableSubmitBtn: true,
        isPush: false,
        mp_isPush: false,
        rr_isPush: false,

        //入力補足
        intensityStyle: intensityStyle,
        intensityComment: intensityComment,

        intensityInfo: '',

        //rideのフォーム入力
        selectedMeetingPlace: '',
        selectedRideRoute: '',
        date: '',
        time: '',
        name: '',
        intensity: 0,
        num_of_laps: 0,
        comment: '',
        publish_status: 0,

        selectedRideRouteKey: '',
        time_appoint: '',


        //meetingPlaceのフォーム入力
        mp_name: '',
        mp_prefecture_code: '',
        mp_address: '',
        mp_publish_status: 2,
        mp_save_status: false,


        //rideRouteのフォーム入力
        rr_name: '',
        rr_elevation: '',
        rr_distance: '',
        rr_lap_status: false,
        rr_comment: '',
        rr_publish_status: 2,
        rr_save_status: false,


        selectedLap_status: false,
    },

    mounted() {
        this.outputError = new OutputError();
        this.fetchMeetingPlaces();
        this.fetchRideRoutes();
    },

    computed :{
        /**
         * selectedMeetingPlaceのバリデーションチェック
         *
         * @returns bool
         */
        isValidSelectedMeetingPlace: function(){
            const uuidLength = 36;
            if(this.selectedMeetingPlace.length == uuidLength){
                return true;

            }else if(this.selectedMeetingPlace.length == 0){
                return false;

            }else{
                return 'create';
            }
        },

        /**
         * selectedRideRouteのバリデーションチェック
         *
         * @returns bool
         */
        isValidSelectedRideRoute: function(){
            if(this.selectedRideRouteKey.length == 0){
                return false;

            }else if(this.selectedRideRouteKey === 'create'){
                return 'create';

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

            }else if(name.length > nameRule.max){
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

            }else if(this.comment.length > commentRule.max){
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
                || this.isNum_of_lapsIsExist == false
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
        },

        /**
         * rr_lap_status == trueの場合num_of_lapsが1から255の間かをチェック
         */
        isNum_of_lapsIsExist: function(){
            let laps = Number(this.num_of_laps);

            if(this.selectedLap_status == true && (laps <= 0 || laps > lapRule.max)){
                return false;

            }else{
                return true;
            }
        }
    },

    watch :{
        /**
         * selectedRideRouteのoption変更時と新規作成時にそれが有効な値(デフォルト、ルート作成option以外)の場合は
         * selectedRideRouteとselectedLap_statusに値を反映する
         */
        selectedRideRouteKey(){
            if(this.isValidSelectedRideRoute == true){
                let rideRoute = this.rideRoutes.data[this.selectedRideRouteKey]

                this.selectedRideRoute = rideRoute.uuid;
                this.selectedLap_status = rideRoute.lap_status;

            }else{
                this.selectedLap_status = false;
                this.num_of_laps = 0;
            }
        },

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
            let key = Number(this.isValidName);

            this.nameClass = this.form_control[key];
            this.nameErrComment = this.getValidationMessage(form, nameRule.max, key);
        },

        /**
         * this.isValidCommentの内容を元にバリデート内容をビューに反映
         */
        isValidComment(){
            const form = 'ライドの説明';
            let key = Number(this.isValidComment);

            this.commentClass = this.form_control[key];
            this.commentErrComment = this.getValidationMessage(form, commentRule.max, key);
        },

        /**
         * isInValidFormsの値をthis.disableSubmitBtnに代入
         */
        isInValidForms(){
            this.disableSubmitBtn = this.isInValidForms;
        },

        /**
         * 値が"create" の場合にモーダルメソッドを呼び出す
         */
        isValidSelectedMeetingPlace(){
            if(this.isValidSelectedMeetingPlace == 'create'){
                const options = {};
                $('#meetingPlaceModal').modal(options);

                this.selectedMeetingPlace = "";
            }
        },

        /**
         * 値が"create" の場合にモーダルメソッドを呼び出す
         */
        isValidSelectedRideRoute(){
            if(this.isValidSelectedRideRoute == 'create'){
                const options = {};
                $('#rideRouteModal').modal(options);

                this.selectedRideRouteKey = "";
            }
        },

        mp_save_status(){
            if(this.mp_save_status){
                this.mp_publish_status = '';

            }else{
                this.rr_publish_status = 2;
            }
        },

        rr_save_status(){
            if(this.rr_save_status){
                this.rr_publish_status = '';

            }else{
                this.rr_publish_status = 2;
            }
        },

    },

    methods :{

        /**
         * 保存した集合場所を取得
         */
        fetchMeetingPlaces: function(){
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
        fetchRideRoutes: function(){
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

        /**
         * 押されたボタンの引数をthis.publish_statusに代入
         *
         * @param {*} val
         */
        mp_inputPublishStatus: function(val){
            this.mp_publish_status = Number(val);
            this.$forceUpdate();
        },

        /**
         * 都道府県コードをmp_prefecture_codeに代入
         *
         * @param {*} val
         */
        mp_inputPrefecture_code: function(val){
            this.mp_prefecture_code = val.target.value;
        },

        /**
         * 押されたボタンの引数をthis.publish_statusに代入
         *
         * @param {*} val
         */
        rr_inputPublishStatus: function(val){
            this.rr_publish_status = Number(val);
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
         */
        submit: function() {
            this.isPush = true;

            const url = 'api/post/createRide';

            let data = {
                "meeting_places_uuid":this.selectedMeetingPlace,
                "ride_routes_uuid":this.selectedRideRoute,
                "name":this.name,
                "time_appoint":this.time_appoint,
                "intensity":this.intensity,
                "num_of_laps":this.num_of_laps,
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

                this.isPush = false;

            }).then(res => {

              if(res.data.status){
                location.href = `../ride?uuid=${res.data.ride_uuid}`;
              }
            });
        },

        /**
         * 集合場所の登録
         *
         * @fetch api/post/meetingPlace
         */
        mp_submit: function(){
            this.mp_isPush = true;

            const url = 'api/post/meetingPlace';

            if(!this.mp_save_status){
                // 集合場所情報を保存しない場合に公開ステータスを非公開に修正
                this.mp_publish_status = 2;
            }

            const data = {
                "name":this.mp_name,
                "prefecture_code":this.mp_prefecture_code,
                "address":this.mp_address,
                "publish_status":this.mp_publish_status,
                "save_status":Boolean(this.mp_save_status)
            }

            const axiosPost = axios.create({
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                withCredentials: true
            });

            axiosPost.post(url, data)
            .catch(({response}) => {
                const error_arr = this.outputError.convert_to_array(response.data.errors);

                this.mp_httpErrors = error_arr;
                this.mp_isPush = false;

            }).then(res => {
                this.mp_isPush = false;
                if(!res){
                    return;
                }

                // 集合場所の作成が成功した場合
                const uuid = res.data.uuid;
                const data = {
                    "uuid"            : uuid,
                    "name"            : this.mp_name,
                    "address"         : this.mp_address,
                    "prefecture_code" : this.mp_prefecture_code,
                };
                this.meetingPlaces.data.push(data);
                this.selectedMeetingPlace = uuid;

                // モーダルのリセット
                this.resetModals();
                $('#meetingPlaceModal').modal('hide');
            });
        },

        /**
         * ライドルートを登録
         *
         * @fetch api/post/rideRoute
         */
        rr_submit: function(){
            this.rr_isPush = true;

            const url = 'api/post/rideRoute';

            if(!this.rr_save_status){
                // ルート情報を保存しない場合に公開ステータスを非公開に修正
                this.rr_publish_status = 2;
            }

            const data = {
                "name":this.rr_name,
                "elevation":this.rr_elevation,
                "distance":this.rr_distance,
                "lap_status":Boolean(this.rr_lap_status),
                "comment":this.rr_comment,
                "publish_status":this.rr_publish_status,
                "save_status":Boolean(this.rr_save_status),
            }

            const axiosPost = axios.create({
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                withCredentials: true
            });
            axiosPost.post(url, data)
            .catch(({response}) => {
                const error_arr = this.outputError.convert_to_array(response.data.errors);

                this.rr_httpErrors = error_arr;
                this.rr_isPush = false;

            }).then(res => {
                this.rr_isPush = false;
                if(!res){
                    return;
                }

                const uuid = res.data.uuid;
                const data = {
                    "name":this.rr_name,
                    "uuid":uuid,
                    "lap_status":this.rr_lap_status
                };

                //
                this.rideRoutes.data.push(data);
                this.selectedRideRouteKey = this.rideRoutes.data.length;

                // モーダルのリセット
                this.resetModals();
                $('#rideRouteModal').modal('hide');
            });
        },

        resetModals: function(){
            this.mp_name = '';
            this.mp_address = '';
            this.mp_publish_status = '';
            this.mp_save_status = '';
            this.rr_name = '';
            this.rr_elevation = '';
            this.rr_distance = '';
            this.rr_lap_status = '';
            this.rr_comment = '';
            this.rr_publish_status = '';
            this.rr_save_status = '';

            this.mp_httpErrors = [];
            this.rr_httpErrors = [];
        },
    },
});
