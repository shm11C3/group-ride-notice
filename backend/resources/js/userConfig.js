import Vue from 'vue';
import axios from 'axios';
import {prefecture} from './constants/constant';
import WindowHelper from './methods/method';

window.axios = require('axios');
const windowHelper = new WindowHelper();

new Vue({
    el: '#app',
    data: {
        //固定値
        prefecture: prefecture,

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
        this.fetchUserProfile();
    },

    methods:{
        /**
         * 表示する画面を変更させる
         *
         * @param {int} page
         */
        changePage: function(page){
            this.pageStatus = page;
            this.listBtnStatus = ['', '', '', ''];
            this.listBtnStatus[page] = 'active';
        },

        /**
         * ユーザのプロフィールをAPIから取得
         */
        fetchUserProfile: function(){
            this.profile_isLoad = true;

            const url = '../api/get/my-profile';

            axios.get(url)
            .catch(error =>{
                console.log(error);
                this.httpErrors.push(error);
                this.profile_isLoad = false;

            }).then(res=>{
                this.profile = res.data[0];
                this.created_at = windowHelper.replaceDate(this.profile.created_at);

                this.replacedUser_introArr = windowHelper.splitByLineFeed(this.profile.user_intro);

                this.profile_isLoad = false;

            });
        },

        /**
         * フォームのデータをオブジェクトにまとめ、アップデートAPIにデータを送信
         */
        profile_update: function(){
            this.httpErrors = '';
            this.update = false;
            this.isPush = true;
            this.closeAllEditForm();

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

        /**
         * Editボタンを押下した時にそれぞれの項目の編集フォームを表示を変える
         * trueの場合フォームを表示、falseでプレビュー画面を表示
         */
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

        /**
         * フォームの編集フォームをすべて閉じる
         */
        closeAllEditForm: function(){
            this.name_formStatus = this.prefecture_formStatus = this.url_formStatus
            = this.fb_username_formStatus = this.tw_username_formStatus
            = this.ig_username_formStatus = this.user_intro_formStatus = false;

            this.replacedUser_introArr = windowHelper.splitByLineFeed(this.profile.user_intro);
        },
    }
});
