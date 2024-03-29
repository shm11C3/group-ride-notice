import Vue from 'vue';
import {passwordRule} from './constants/user'

new Vue({
    el: '#app',
    data: {
        disableSubmitBtn: true,
        inputDelete: '',
        password: '',
    },

    computed: {
        isValidInput: function(){
            return (this.inputDelete == '完全に削除' && this.password.length >= passwordRule.min);
        }
    },

    watch: {
        isValidInput(){
            this.disableSubmitBtn = !this.isValidInput;
        }
    }
});
