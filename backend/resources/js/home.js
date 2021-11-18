import Vue from 'vue';
import jQuery, { data } from 'jquery'
import {prefecture} from './constants/constant'
global.jquery = jQuery
global.$ = jQuery
window.$ = window.jQuery = require('jquery')
window.axios = require('axios');

new Vue({
    el: '#app',
    data: {
        //データ
        rides: [],
        next_ride: '',

        participateIndex: '',
        participateComment: '',

        prefecture: prefecture,

        //パラメータ
        page: 1,
        time_appoint: 0,
        prefecture_code: 0,
        intensity: 0,
        filterFollow: 0,


        //状態
        isLoad: false,
        next_isLoad: true,
        participateModal: false,
        pt_isPush: false,


        //エラー表示
        httpErrors: [],

        observer: null,

        resIsExist: false,
        resNextIsExist: false,

        authUser: '',
    },

    mounted() {
        this.initialLoad();

        this.observer = new IntersectionObserver((entries) => {
            const entry = entries[0];

            if (entry && entry.isIntersecting && this.resIsExist && !this.next_isLoad) {
                this.addLoad();
            }
        });

        const observe_element = this.$refs.observe_element;
        this.observer.observe(observe_element);
    },

    methods :{
        /**
         * ライド取得の呼び出し
         */
        initialLoad: function(){
            this.rides = [];
            this.page = 1;
            this.fetchRides();
        },
        addLoad: function (){
            this.page++;
            this.fetchRides();
        },

        /**
         * ライドの取得
         */
        fetchRides: function(){
            this.isLoad = true;

            const url = `/api/get/rides/${this.time_appoint}/${this.prefecture_code}/${this.intensity}/${Number(this.filterFollow)}?page=${this.page}`;

            axios.get(url)
            .catch(error =>{
                console.log(error);
                this.httpErrors.push(error);
                this.isLoad = false;

            }).then(res =>{
                const auth = Boolean(res.data.user_uuid);

                const rides_data = res.data.rides;
                if(rides_data.data){
                    rides_data.data.forEach(ride => this.rides.push(ride));
                }

                this.resIsExist = Boolean(rides_data.next_page_url);

                this.isLoad = false;

                if(auth && !this.resNextIsExist){
                    this.getNextRide();
                }
            });
        },

        /**
         * 次回参加ライドを取得
         */
         getNextRide: function(){
            this.next_isLoad = true;
            const self = this;

            let url = `/api/get/my-rides/0?page=1`;

            axios.get(url)
            .catch(error =>{
                console.log(error);
                this.httpErrors.push(error);
                this.isLoad = false;

            }).then(res =>{
                let data = res.data.rides.data;

                this.next_isLoad = false;
                this.resNextIsExist = Boolean(data.length);

                this.authUser = res.data.user_uuid;
                this.next_ride = data[0];
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

        input_filterFollow: function(){
            this.initialLoad();
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
