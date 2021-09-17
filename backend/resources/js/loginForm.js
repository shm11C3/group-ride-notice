window.$ = window.jQuery = require('jquery');
window.Popper = require('popper.js');
import './bootstrap';
import Vue from 'vue';

new Vue({
    el: '#app',
    data: {
        submitStatus: false,
        email: '',
        password: '',
    },

    mounted() {
        
    },

    computed: {

        isInput: function() {
            if(this.email && this.password && this.isInValidEmail) {
                return true;
            }else{
                return false;
            }
        },

        isInValidEmail: function() {
            const reg = new RegExp(/^[A-Za-z0-9]{1}[A-Za-z0-9_.-]*@{1}[A-Za-z0-9_.-]{1,}\.[A-Za-z0-9]{1,}$/);

            if(reg.test(this.email)){
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