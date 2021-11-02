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
            return (this.inputDelete == 'delete_me' && this.password.length >= passwordRule.min);
        }
    },

    watch: {
        isValidInput(){
            this.disableSubmitBtn = !this.isValidInput;
        }
    }
});
