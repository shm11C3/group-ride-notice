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
        initialLoad: function(){
            this.rides = [];
            this.page = 1;
            this.getRides();
        },
        addLoad: function (){
            this.page++;
            this.getRides();
        },

        /**
         * ライドの取得
         */
         getRides: function(){
            this.isLoad = true;
            const self = this;

            let url = `/api/get/my-rides/1?page=${this.page}`;

            axios.get(url)
            .catch(error =>{
                console.log(error);
                this.httpErrors.push(error);
                this.isLoad = false;

            }).then(res =>{
                let data = res.data;

                data.rides.data.forEach(element => this.rides.push(element));
                this.authUser = data.user_uuid;

                this.isLoad = false;
                this.resIsExist = Boolean(data.rides.next_page_url);
            });
        },
    }
});
