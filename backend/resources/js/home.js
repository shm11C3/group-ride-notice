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


        //パラメータ
        page: 1,
        time_appoint: 0,
        prefecture_code: 0,
        intensity: 0,


        //状態
        isLoad: false,
    },

    mounted() {
        this.load();
    },

    computed :{
        
    },

    watch :{

    },

    methods :{
        /**
         * 
         */
        load: function(){
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
                console.log(res);
                console.log(res.data)
                res.data.data.forEach(element => this.rides.push(element));
                this.isLoad = false;
            });
        },

        input_time_appoint: function(val){
            this.time_appoint = val.target.value;
            this.load();
        },

        input_prefecture_code: function(val){
            this.prefecture_code = val.target.value;
            this.load();
        },

        input_intensity: function(val){
            this.intensity = val.target.value;
            this.load();
        },
    },
});