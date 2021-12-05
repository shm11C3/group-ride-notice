import Vue from 'vue';
import {prefecture} from './constants/constant'
import {getQueryParam} from './methods/method'
import Validation from './methods/validation'

const validation = new Validation();

new Vue({
    el: '#app',
    data: {
        // インポートした固定値
        prefecture_arr: prefecture,

        // ユーザ情報
        uuid: '',
        name: '',
        user_profile_img_path: '',
        prefecture_code: 0,

        // HTMLクラス属性
        nameClass: '',

        // バリデーション
        isNameValid: false,
    },

    mounted(){
        this.getUserDataByQueryParam();
    },

    computed: {
        /**
         * すべてのフォームの値が正常な場合にtrueを返す
         *
         * @param void
         * @return bool
         */
        submitStatus: function (){
            return (this.isNameValid && validation.isPrefecture_codeValid(this.prefecture_code));
        }
    },

    watch: {
        /**
         * nameを監視しバリデーション処理を実行
         *
         * @param void
         * @return bool
         */
        name() {
            this.isNameValid = validation.isNameValid(this.name);

            if(this.name.length === 0){
                this.nameClass = 'form-control';

            }else if(this.isNameValid){
                this.nameClass = 'form-control is-valid';

            }else{
                this.nameClass = 'form-control is-invalid';
            }
        }
    },

    methods: {
        /**
         * URLのクエリパラメータからユーザデータを取得し、dataに格納
         *
         * @param void
         * @return void
         */
        getUserDataByQueryParam: function(){
            const query = getQueryParam();

            this.uuid = query[0].split("=").pop();
            this.name = query[1].split("=").pop();
            const img_arr = query[2].split("=");
            this.user_profile_img_path = `${img_arr[1]}=${img_arr[2]}`;
        }
    }
});
