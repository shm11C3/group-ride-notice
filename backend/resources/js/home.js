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


        //パラメータ
        page: 1,
        time_appoint: 0,
        prefecture_code: 0,
        intensity: 0,


        //状態
        isLoad: false,
        participateModal: false,


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
                
                if(data.length<10){
                    this.resIsExist = false;

                }else{
                    this.resIsExist = true;
                }
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
    
        participate: function(uuid, index){

            this.participateIndex = index;
            this.participateModal = true;

            const options = {};
            $('#participateModal').modal(options);

        }
    },
});