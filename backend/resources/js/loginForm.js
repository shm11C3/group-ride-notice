import Vue from 'vue';
import {passwordRule, emailRule} from './constants/user'

const reg = emailRule.reg;

new Vue({
    el: '#app',
    data: {
        submitStatus: false,
        email: '',
        password: '',
    },

    computed: {

        isInput: function() {
            if(this.email && this.password.length >= passwordRule.min && this.isInValidEmail) {
                return true;
            }
        },

        isInValidEmail: function() {
            if(reg.test(this.email) || this.email.length > emailRule.max){
                return true;
            }
        }
    },

    watch: {
        isInput(){
            this.submitStatus = this.isInput;
        }
    },
});
