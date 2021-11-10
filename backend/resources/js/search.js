import Vue from 'vue';
import {prefecture} from './constants/constant'

new Vue({
    el: '#app',
    data: {
        option: 0,
        option_arr:['all', 'rides', 'users'],
        page: 1,
        acceptAddLoad: false,

        prefecture: prefecture,

        searchWord: '',
        canSubmit: false,
        isLoad: false,
        dataIsExist: false,
        httpErrors: [],

        rides: [],
        users: [],

        replacedUser_intro: '',

        nextRidesIsExist: true,
        nextUsersIsExist: true,
    },

    mounted() {
        this.observer = new IntersectionObserver((entries) => {
            const entry = entries[0];

            if (entry && entry.isIntersecting && this.dataIsExist && this.acceptAddLoad) {
                this.addLoad();
            }
        });

        const observe_element = this.$refs.observe_element;
        this.observer.observe(observe_element);
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

            this.initialize();
            this.fetchSearch();
        },
        initialize: function() {
            this.option = 0;
            this.page = 1;
            this.rides = [];
            this.users = [];
            this.httpErrors = [];
            this.acceptAddLoad = false;
            this.dataIsExist = false;
            this.nextRidesIsExist = true;
            this.nextUsersIsExist = true;
        },

        fetchSearch: function() {
            this.isLoad = true;

            const url = `api/search/${this.searchWord}/${this.option_arr[this.option]}?page=${this.page}`;

            fetch(url)
            .then(response => {
                return response.json();
            })
            .then(res => {
                res.rides.data.forEach(el => this.rides.push(el));
                res.users.data.forEach(el => this.users.push(el));

                if (!res.rides.next_page_url) {
                    this.nextRidesIsExist = false;
                }

                if (!res.users.next_page_url) {
                    this.nextUsersIsExist = false;
                }

                this.dataIsExist = Boolean(res.rides.data.length + res.users.data.length);


            }).catch(e => {
                console.log(e);
                this.httpErrors.push(e);
            });

            this.isLoad = false;
        },

        addLoad: function(){
            this.page++;
            this.fetchSearch();
        },

        /**
         * 更に読み込むボタン押下時
         *
         * @param {*} option
         */
        addLoadBtn: function(option){
            this.option = option;
            this.acceptAddLoad = true;
            this.addLoad();
        }
    }
});
