import Vue from 'vue';

new Vue({
    el: '#app',
    data: {
        disableSubmitBtn: true,
        inputDelete: '',
        password: '',
    },

    computed: {
        isValidInput: function(){
            return (this.inputDelete == 'delete_me' && this.password.length >= 8);
        }
    },

    watch: {
        isValidInput(){
            this.disableSubmitBtn = !this.isValidInput;
        }
    }
});