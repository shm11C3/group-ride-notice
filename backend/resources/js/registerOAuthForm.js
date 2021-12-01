import Vue from 'vue';
import {nameRule} from './constants/user'
import {prefecture} from './constants/constant'
import {getQueryParam} from './methods/method'
import Validation from './methods/validation'

const validation = new Validation();

new Vue({
    el: '#app',
    data: {
        uuid: '',
        name: '',
        user_profile_img_path: '',
        prefecture_arr: '',
        nameClass: '',
        submitStatus: false
    },

    mounted(){
        this.prefecture_arr = prefecture;
        this.getUserDataByQueryParam();

        console.log(validation.test())
    },

    methods: {
        /**
         *
         */
        getUserDataByQueryParam: function(){
            const query = getQueryParam();

            this.uuid = query[0].split("=").pop();
            this.name = query[1].split("=").pop();
            const img_arr = query[2].split("=");
            this.user_profile_img_path = `${img_arr[1]}=${img_arr[2]}`;
        }
    }
});
