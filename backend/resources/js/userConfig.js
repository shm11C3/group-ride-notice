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
        //固定値
        prefecture:["北海道","青森県","岩手県","宮城県","秋田県","山形県","福島県",
                    "茨城県","栃木県","群馬県","埼玉県","千葉県","東京都","神奈川県",
                    "新潟県","富山県","石川県","福井県","山梨県","長野県","岐阜県",
                    "静岡県","愛知県","三重県","滋賀県","京都府","大阪府","兵庫県",
                    "奈良県","和歌山県","鳥取県","島根県","岡山県","広島県","山口県",
                    "徳島県","香川県","愛媛県","高知県","福岡県","佐賀県","長崎県",
                    "熊本県","大分県","宮崎県","鹿児島県","沖縄県"],

        //パラメータ
        user_uuid: '',

        //画面偏移
        pageStatus: 0,
        listBtnStatus: ['active', '', '', ''],
        profile_isLoad: true,

        //HTTP Error
        httpErrors: [],

        //データ
        profile: '',
        created_at: '',

        replacedUser_introArr: '',

        name_formStatus: false,
        prefecture_formStatus: false,
        url_formStatus: false,
        fb_username_formStatus: false,
        tw_username_formStatus: false,
        ig_username_formStatus: false,
        user_intro_formStatus: false,

        isPush: false,
        update: false,
    },

    mounted(){
        this.getUserProfile();
    },

    methods:{
        /**
         * pageStatus変更
         */
        changePage: function(page){
            this.pageStatus = page;
            this.listBtnStatus = ['', '', '', ''];
            this.listBtnStatus[page] = 'active';
        },

        getUserProfile: function(){
            this.profile_isLoad = true;

            const url = '../api/get/my-profile';

            axios.get(url)
            .catch(error =>{
                console.log(error);
                this.httpErrors.push(error);
                this.profile_isLoad = false;

            }).then(res=>{
                this.profile = res.data[0];
                this.created_at = this.replaceDate(this.profile.created_at);

                if(this.profile.user_intro){
                    this.replacedUser_introArr = this.profile.user_intro.split(/\r\n|\n/);
                }
                
                this.profile_isLoad = false;

            });
        },

        profile_update: function(){
            this.httpErrors = '';
            this.update = false;
            this.isPush = true;
            this.closeAllForm();

            const url = '../api/post/profile/update';

            const data = {
                "name": this.profile.name,
                "prefecture_code": this.profile.prefecture_code,
                "user_intro": this.profile.user_intro,
                "user_url": this.profile.user_url,
                "fb_username": this.profile.fb_username,
                "tw_username": this.profile.tw_username,
                "ig_username": this.profile.ig_username,
            }

            let axiosPost = axios.create({
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                withCredentials: true
            });
  
            axiosPost.post(url, data)
              
            .catch(({response}) => {
                const errors = response.data.errors;
                let errorArr = [];
                
                Object.keys(errors).forEach(function(key) { 
                    errorArr.push(errors[key][0]);
                });

                this.httpErrors = errorArr;
                 
                this.isPush = false;
                this.update = false;
                
              }).then(res => {
                this.isPush = false;
                if(res){
                    this.update = true;
                }
              });
        },

        name_openUpdate: function(){
            this.name_formStatus = !this.name_formStatus;
        },
        prefecture_openUpdate: function(){
            this.prefecture_formStatus = !this.prefecture_formStatus;
        },
        url_openUpdate: function(){
            this.url_formStatus = !this.url_formStatus;
        },
        fb_username_openUpdate: function(){
            this.fb_username_formStatus = !this.fb_username_formStatus;
        },
        tw_username_openUpdate: function(){
            this.tw_username_formStatus = !this.tw_username_formStatus;
        },
        ig_username_openUpdate: function(){
            this.ig_username_formStatus = !this.ig_username_formStatus;
        },
        user_intro_openUpdate: function(){
            this.user_intro_formStatus = !this.user_intro_formStatus;
        },

        closeAllForm: function(){
            this.name_formStatus = this.prefecture_formStatus = this.url_formStatus 
            = this.fb_username_formStatus = this.tw_username_formStatus 
            = this.ig_username_formStatus = this.user_intro_formStatus = false;

            if(this.profile.user_intro){
                this.replacedUser_introArr = this.profile.user_intro.split(/\r\n|\n/);
            }
        },

        replaceDate: function(dateParam){
            let year = dateParam.substring(0,4)+'年';
            let date = dateParam.substring(5,7)+'月'+dateParam.substring(8,10)+'日';
            let time = dateParam.substring(10,16);

            return [year, date, time];
        }
    }
});