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
                
                if(data.length<30){
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
            
            //[todo]モーダルを閉じる際にthis.participateIndex, this.participateModalの値を初期化
        },
    },
});