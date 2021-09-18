window.$ = window.jQuery = require('jquery');
window.Popper = require('popper.js');
import 'jquery';
import 'popper.js';
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
            if(this.email && this.password.length >= 6 && this.isInValidEmail) {
                return true;
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