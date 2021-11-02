import Vue from 'vue';
import jQuery, { data } from 'jquery'
import axios from 'axios';
import {prefecture} from './constants/constant'
global.jquery = jQuery
global.$ = jQuery
window.$ = window.jQuery = require('jquery')
window.axios = require('axios');

new Vue({
    el: '#app',
    data: {
        prefecture: prefecture,

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
