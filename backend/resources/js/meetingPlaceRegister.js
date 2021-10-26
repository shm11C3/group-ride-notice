import Vue from 'vue';
import jQuery, { data } from 'jquery';
global.jquery = jQuery;
global.$ = jQuery;
window.$ = window.jQuery = require('jquery');
window.axios = require('axios');

new Vue({
    el: '#app',
    data: {
        meeting_place_page: 1,
        meetingPlaces: [],
        isLoad: false,
        prefecture_code_request: 0,
        resIsExist: false,
        auth_uuid: '',
        saveMeetingPlaceStatus: '',

        prefecture:["北海道","青森県","岩手県","宮城県","秋田県","山形県","福島県",
                    "茨城県","栃木県","群馬県","埼玉県","千葉県","東京都","神奈川県",
                    "新潟県","富山県","石川県","福井県","山梨県","長野県","岐阜県",
                    "静岡県","愛知県","三重県","滋賀県","京都府","大阪府","兵庫県",
                    "奈良県","和歌山県","鳥取県","島根県","岡山県","広島県","山口県",
                    "徳島県","香川県","愛媛県","高知県","福岡県","佐賀県","長崎県",
                    "熊本県","大分県","宮崎県","鹿児島県","沖縄県"],

    },

    mounted() {
        this.initialLoad();

        this.observer = new IntersectionObserver((entries) => {
            const entry = entries[0];

            if (entry && entry.isIntersecting && this.resIsExist) {
                this.addLoad();
            }
        });

        const observe_element = this.$refs.observe_element;
        this.observer.observe(observe_element);
    },

    methods: {
        /**
         * 集合場所取得の呼び出し
         */
        initialLoad: function(){
            this.meetingPlaces = [];
            this.meeting_place_page = 1;
            this.fetchMeetingPlaces();
        },
        addLoad: function (){
            this.meeting_place_page++;
            this.fetchMeetingPlaces();
        },

        /**
         * 集合場所リストを取得
         */
        fetchMeetingPlaces: function(){
            this.isLoad = true;

            const url = `http://www.localhost/api/get/meeting-places/${this.prefecture_code_request}?page=${this.meeting_place_page}`;

            axios.get(url)
            .catch(error =>{
                this.isLoad = false;

                console.error(error);

            }).then(res =>{
                this.isLoad = false;
                const data = res.data;

                this.auth_uuid = data.auth_uuid;
                this.meetingPlaces = data.meeting_places.data;

                this.resIsExist = Boolean(data.meeting_places.next_page_url)
            });
        },

        /**
         * 都道府県による取得条件の変更
         *
         * @param {*} val
         */
        input_prefecture_code: function(val){
            this.prefecture_code_request = val.target.value;
            this.initialLoad();
        },

        saveMeetingPlace: function(i){
            const meeting_place_uuid = this.meetingPlaces[i].uuid;
            const url = '../../api/post/registerMeetingPlace';

            const data = {
                "meeting_place_uuid" : meeting_place_uuid
            }

            const axiosPost = axios.create({
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                withCredentials: true
            });

            axiosPost.post(url, data)
            .catch(error => {
                console.error(error);

            }).then(res => {
                this.saveMeetingPlaceStatus = res.data.status;
            });
        }
    }
});
