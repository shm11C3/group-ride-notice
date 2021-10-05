import Vue from 'vue';
import jQuery, { data } from 'jquery'
global.jquery = jQuery
global.$ = jQuery
window.$ = window.jQuery = require('jquery')
window.axios = require('axios');

new Vue({
    el: '#app',
    data: {
        //パラメーター
        request_uuid: '',

        //データ
        ride: '',

        prefecture:["北海道","青森県","岩手県","宮城県","秋田県","山形県","福島県",
                    "茨城県","栃木県","群馬県","埼玉県","千葉県","東京都","神奈川県",
                    "新潟県","富山県","石川県","福井県","山梨県","長野県","岐阜県",
                    "静岡県","愛知県","三重県","滋賀県","京都府","大阪府","兵庫県",
                    "奈良県","和歌山県","鳥取県","島根県","岡山県","広島県","山口県",
                    "徳島県","香川県","愛媛県","高知県","福岡県","佐賀県","長崎県",
                    "熊本県","大分県","宮崎県","鹿児島県","沖縄県"],

        publish_status_arr: ['公開', '限定公開', '非公開'],

        isLoad: false,

        httpErrors: [],
        authError: false,
    },

    mounted() {        
        const param = this.getQueryParam();
        this.getRide(param);
    },

    methods: {
        /**
         * 
         */
        getQueryParam: function(){
            const param = location.search;

            return param.substr(6,36)
        },

        /**
         * ライドの取得
         */
         getRide: function(request_uuid){
            this.isLoad = true;
            const self = this;

            let url = `/api/get/my-ride/${request_uuid}`;

            axios.get(url)
            .catch(error =>{
                console.log(error);
                this.httpErrors.push(error);
                this.isLoad = false;

            }).then(res =>{
                let data = res.data;
                this.authError = Boolean(data.length);

                this.ride = data;            
            });
        },
    }


});