import Vue from 'vue';

new Vue({
    el: '#app',
    data: {
        disableSubmitBtn: true,
        inputDelete: '',
    },

    computed: {
        isValidInput: function(){
            return (this.inputDelete == '完全に削除');
        }
    },

    watch: {
        isValidInput(){
            this.disableSubmitBtn = !this.isValidInput;
        }
    }
});
