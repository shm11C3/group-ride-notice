import Vue from 'vue';
import jQuery, { data } from 'jquery'
import axios from 'axios';
global.jquery = jQuery
global.$ = jQuery
window.$ = window.jQuery = require('jquery')
window.axios = require('axios');

new Vue({
    el: '#app',
    data: {
        prefecture:["北海道","青森県","岩手県","宮城県","秋田県","山形県","福島県",
                    "茨城県","栃木県","群馬県","埼玉県","千葉県","東京都","神奈川県",
                    "新潟県","富山県","石川県","福井県","山梨県","長野県","岐阜県",
                    "静岡県","愛知県","三重県","滋賀県","京都府","大阪府","兵庫県",
                    "奈良県","和歌山県","鳥取県","島根県","岡山県","広島県","山口県",
                    "徳島県","香川県","愛媛県","高知県","福岡県","佐賀県","長崎県",
                    "熊本県","大分県","宮崎県","鹿児島県","沖縄県"],

        searchWord: '',
        canSubmit: false,
        isLoad: false,
        dataIsExist: false,
        httpErrors: [],

        rides: [],
        users: [],

        replacedUser_intro: '',
    },

    methods: {
        enable_submit: function() {
            this.canSubmit = true;
        },
        disable_submit: function() {
            this.canSubmit = false;
        },
        submit: function(){
            if (!this.canSubmit || this.isLoad || !this.searchWord) return;
            this.isLoad = true;

            this.rides = [];
            this.users = [];
            
            let url = `api/search/${this.searchWord}`;

            axios.get(url)
            .catch(error =>{
                console.error(error);
                this.httpErrors.push(error);
                this.isLoad = false;

            }).then(res =>{
                this.rides = res.data.rides.data;
                this.users = res.data.users.data;

                this.dataIsExist = !Boolean(this.rides.length + this.users.length);
                
                this.isLoad = false;
            });
        },

        openCancelParticipateModal: function(){

        }
    }
});