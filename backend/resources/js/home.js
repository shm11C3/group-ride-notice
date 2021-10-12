import Vue from 'vue';
import jQuery, { data } from 'jquery'
global.jquery = jQuery
global.$ = jQuery
window.$ = window.jQuery = require('jquery')
window.axios = require('axios');

new Vue({
    el: '#app',
    data: {
        //データ
        rides: [],

        participateIndex: '',
        participateComment: '',

        prefecture:["北海道","青森県","岩手県","宮城県","秋田県","山形県","福島県",
                    "茨城県","栃木県","群馬県","埼玉県","千葉県","東京都","神奈川県",
                    "新潟県","富山県","石川県","福井県","山梨県","長野県","岐阜県",
                    "静岡県","愛知県","三重県","滋賀県","京都府","大阪府","兵庫県",
                    "奈良県","和歌山県","鳥取県","島根県","岡山県","広島県","山口県",
                    "徳島県","香川県","愛媛県","高知県","福岡県","佐賀県","長崎県",
                    "熊本県","大分県","宮崎県","鹿児島県","沖縄県"],


        //パラメータ
        page: 1,
        time_appoint: 0,
        prefecture_code: 0,
        intensity: 0,


        //状態
        isLoad: false,
        participateModal: false,

        pt_isPush: false,


        //エラー表示
        httpErrors: [],

        observer: null,

        resIsExist: false,
    },

    mounted() {
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

    computed :{
        
    },

    watch :{

    },

    methods :{
        /**
         * 
         */
        initialLoad: function(){
            this.rides = [];
            this.page = 1;
            this.getRides();
        },
        
        /**
         * ライドの取得
         */
        getRides: function(){
            this.isLoad = true;
            const self = this;

            let url = `/api/get/rides/${this.time_appoint}/${this.prefecture_code}/${this.intensity}?page=${this.page}`;

            axios.get(url)
            .catch(error =>{
                console.log(error);
                this.httpErrors.push(error);
                this.isLoad = false;

            }).then(res =>{
                let data = res.data.data;
                
                data.forEach(element => this.rides.push(element));
                this.isLoad = false;
                
                this.resIsExist = Boolean(res.data.next_page_url);
            });
        },

        input_time_appoint: function(val){
            this.time_appoint = val.target.value;
            this.initialLoad();
        },

        input_prefecture_code: function(val){
            this.prefecture_code = val.target.value;
            this.initialLoad();
        },

        input_intensity: function(val){
            this.intensity = val.target.value;
            this.initialLoad();
        },

        addLoad: function (){
            this.page++;
            this.getRides();
        },
    
        openParticipateModal: function(index){
            this.participateComment = 'よろしくお願いします。';
            this.participateIndex = index;
            this.participateModal = true;

            const options = {};
            $('#participateModal').modal(options);
        },

        closeParticipateModal: function(){

            this.participateIndex = '';
            this.participateComment = '';
            this.participateModal = false;

            $('#participateModal').modal('hide');
            $('#cancelParticipateModal').modal('hide');
        },

        openCancelParticipateModal: function(index){
            this.participateIndex = index;
            this.participateModal = false;


            const options = {};
            $('#cancelParticipateModal').modal(options);
        },

        participation: function(){
            this.pt_isPush = true;

            const url = 'api/post/participation';

            let data = {
                "ride_uuid" : this.rides[this.participateIndex].uuid,
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

                this.rides[this.participateIndex].rideParticipant_user = true;
                this.rides[this.participateIndex].rideParticipant_count++;

                this.closeParticipateModal();    
                this.pt_isPush = false;
            });
        },

        cancelParticipation: function(){
            this.pt_isPush = true;

            const url = 'api/post/participation/delete';

            let data = {
                "ride_uuid" : this.rides[this.participateIndex].uuid,
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

                this.rides[this.participateIndex].rideParticipant_user = false;
                this.rides[this.participateIndex].rideParticipant_count--;

                this.closeParticipateModal();
                this.pt_isPush = false;
            });
        },
    },
});