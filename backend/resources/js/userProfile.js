import Vue from "vue";
import {postFollow} from './methods/fetch';
import {getLastPass} from './methods/method'

new Vue({
    el: '#app',
    data: {
        user_to : '',
        followBtnStatus: true
    },

    mounted() {
        this.user_to = getLastPass();
    },

    methods: {
        /**
         * フォロー/フォロー解除ボタン押下時
         */
        follow: function(){
            postFollow(this.user_to);
            this.followBtnStatus = !this.followBtnStatus;
        }
    }
})
