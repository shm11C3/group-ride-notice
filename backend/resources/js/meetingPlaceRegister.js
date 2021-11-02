import Vue from 'vue';
import jQuery, { data } from 'jquery';
import {prefecture} from './constants/constant'
global.jquery = jQuery;
global.$ = jQuery;
window.$ = window.jQuery = require('jquery');
window.axios = require('axios');

new Vue({
    el: '#app',
    data: {
        meeting_place_page: 1,
        meetingPlaces: [],
        isLoad: false,
        prefecture_code_request: 0,
        resIsExist: false,
        auth_uuid: '',
        saveMeetingPlaceStatus: '',

        prefecture: prefecture,

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

    methods: {
        /**
         * 集合場所取得の呼び出し
         */
        initialLoad: function(){
            this.meetingPlaces = [];
            this.meeting_place_page = 1;
            this.fetchMeetingPlaces();
        },
        addLoad: function (){
            this.meeting_place_page++;
            this.fetchMeetingPlaces();
        },

        /**
         * 集合場所リストを取得
         */
        fetchMeetingPlaces: function(){
            this.isLoad = true;

            const url = `../api/get/meeting-places/${this.prefecture_code_request}?page=${this.meeting_place_page}`;

            axios.get(url)
            .catch(error =>{
                this.isLoad = false;

                console.error(error);

            }).then(res =>{
                this.isLoad = false;
                const data = res.data;

                if(data.auth_uuid){
                    this.auth_uuid = data.auth_uuid;

                    data.meeting_places.forEach(element => {
                        this.meetingPlaces.push(element);
                    });

                    this.resIsExist = Boolean(data.next_page_url);

                    this.$forceUpdate();
                }else{
                    this.resIsExist = false;
                }
            });
        },

        /**
         * 都道府県による取得条件の変更
         *
         * @param {*} val
         */
        input_prefecture_code: function(val){
            this.prefecture_code_request = val.target.value;
            this.initialLoad();
        },

        /**
         *
         * @param {*} i
         */
        saveMeetingPlace: function(i){
            this.saveMeetingPlaceStatus = '';

            const meeting_place_uuid = this.meetingPlaces[i].data.uuid;
            const url = '../../api/post/registerMeetingPlace';

            const data = {
                "meeting_place_uuid" : meeting_place_uuid
            }

            const axiosPost = axios.create({
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                withCredentials: true
            });

            axiosPost.post(url, data)
            .catch(error => {
                console.error(error);

            }).then(res => {
                this.saveMeetingPlaceStatus = res.data.status;
                this.meetingPlaces[i].isRegistered = res.data.status;
            });
        }
    }
});
