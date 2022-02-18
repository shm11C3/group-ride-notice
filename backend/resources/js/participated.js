import Vue from 'vue';
import jQuery, { data } from 'jquery'
import {prefecture} from './constants/constant'
import LoadImage from './methods/loadImage';

global.jquery = jQuery
global.$ = jQuery
window.$ = window.jQuery = require('jquery')
window.axios = require('axios');

new Vue({
    el: '#app',
    data: {
        //データ
        rides: [],
        authUser: '',

        prefecture: prefecture,

        //パラメータ
        page: 1,
        time_appoint: 0,
        prefecture_code: 0,
        intensity: 0,


        //状態
        isLoad: false,

        //エラー表示
        httpErrors: [],

        observer: null,

        resIsExist: false,

        isImgLoaded: [],
        opacity: [],
    },

    mounted() {
        this.loadImage = new LoadImage();
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

            let url = `/api/get/my-rides/1?page=${this.page}`;

            axios.get(url)
            .catch(error =>{
                console.log(error);
                this.httpErrors.push(error);
                this.isLoad = false;

            }).then(res =>{
                let data = res.data;

                data.rides.data.forEach(element => {
                    this.rides.push(element);
                    this.opacity.push(this.loadImage.default_ride_opacity);
                    this.isImgLoaded.push(false);
                });
                this.authUser = data.user_uuid;

                this.isLoad = false;
                this.resIsExist = Boolean(data.rides.next_page_url);
            });
        },

        load_img: function(i){
            this.isImgLoaded[i] = true;
            this.opacity[i] = '';
            this.$forceUpdate();
        },
    }
});
