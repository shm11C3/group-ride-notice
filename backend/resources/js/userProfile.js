import Vue from "vue";
import {postFollow, fetchFollows, fetchFollowers, fetchUserRides} from './methods/fetch';
import {getLastPass} from './methods/method'
import {prefecture} from './constants/constant'

const active_tab_class = 'active user-tab-active';

new Vue({
    el: '#app',
    data: {
        prefecture: prefecture,
        active_tab_class: active_tab_class,

        user_uuid : '',
        followBtnStatus: true,

        user_info_class: [active_tab_class, '', '', ''],

        userRides_index: 0,
        follows_index: 1,
        followers_index: 2,

        userRides: [],
        follows: [],
        followers: [],

        userRides_page: 1,
        follows_page: 1,
        followers_page: 1,


        activeTab: 0,

        isLoad: true,

        resIsExist: true,
        next_is_exist: true,
    },

    mounted() {
        this.user_uuid = getLastPass();
        this.fetchUserRides();

        this.observer = new IntersectionObserver((entries) => {
            const entry = entries[0];

            if (entry && entry.isIntersecting && this.next_is_exist && !this.isLoad) {
                this.addLoad();
            }
        });

        const observe_element = this.$refs.observe_element;
        this.observer.observe(observe_element);
    },

    methods: {
        /**
         * フォロー/フォロー解除ボタン押下時
         */
         follow: function(user_uuid, index){
            postFollow(user_uuid);

            if(index == false){
                this.followBtnStatus = !this.followBtnStatus;

            }else if(this.activeTab == this.follows_index){
                this.follows[index].userFollowed = !this.follows[index].userFollowed;

            }else{
                this.followers[index].userFollowed = !this.followers[index].userFollowed;
            }
        },

        /**
         * タブの見た目を変化させる
         *
         * @param {int} index
         */
        changeTabView: function (index){
            this.user_info_class[this.activeTab] = '';
            this.user_info_class[index] = this.active_tab_class;
            this.activeTab = index;
        },

        /**
         * タブ変更時のロード
         *
         * @param {int} index
         */
        initialLoad: function(index){
            this.changeTabView(index);

            if(index == this.userRides_index && this.userRides.length == []){
                // ユーザを取得していない場合
                this.fetchUserRides();

            }else if(index == this.follows_index && this.follows.length == []){
                // フォローユーザを取得していない場合
                this.fetchFollows();

            }else if(index == this.followers_index && this.followers.length == []){
                // フォロワーを取得していない場合
                this.fetchFollowers();
            }
        },

        /**
         * ページ最下部到達時の追加ロード
         */
        addLoad: function(){
            if(this.activeTab == this.userRides_index){
                this.userRides_page++;
                this.fetchUserRides();

            }else if(this.activeTab == this.follows_index){
                this.follows_page++;
                this.fetchFollows();

            }else if(this.activeTab == this.followers_index){
                this.followers_page++;
                this.fetchFollowers();
            }
        },

        /**
         * ユーザの投稿するライドを取得
         */
        fetchUserRides: function(){
            this.isLoad = true;
            fetchUserRides(this.user_uuid, this.userRides_page).then(res => {
                this.next_is_exist = Boolean(res.next_page_url);
                res.data.forEach(ride => this.userRides.push(ride));
                this.isLoad = false;

                this.$forceUpdate();
            });
        },

        /**
         * フォロー中のユーザーを取得
         */
        fetchFollows: function(){
            this.isLoad = true;
            fetchFollows(this.user_uuid, this.follows_page).then(res => {
                this.next_is_exist = Boolean(res.next_page_url);
                res.data.forEach(follow => this.follows.push(follow));
                this.isLoad = false;

                this.$forceUpdate();
            });
        },

        /**
         * フォロワーのユーザ－を取得
         */
        fetchFollowers: function(){
            this.isLoad = true;
            fetchFollowers(this.user_uuid, this.followers_page).then(res => {
                this.next_is_exist = Boolean(res.next_page_url);
                res.data.forEach(follower => this.followers.push(follower));
                this.isLoad = false;

                this.$forceUpdate();
            });
        },
    }
});
