import Vue from 'vue';

new Vue({
    el: '#app',
    data: {
        submitStatus: false,
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        nameClass: 'form-control',
        emailClass: 'form-control',
        passwordClass: 'form-control',
        confirmClass: 'form-control',
        error: '',
        isInValidPassword: false,
        passwordError: '',
    },

    computed: {

        isValidForm: function() {
            if(this.email && this.password.length >= 6 && this.isValidEmail && this.confirmPassword) {
                return true;
            }
        },

        isValidName: function() {
            if(this.name.length > 0 && this.name.length < 32){
                return true
            }else{
                return false;
            }
        },

        isValidEmail: function() {
            const email = this.email;
            const reg = new RegExp(/^[A-Za-z0-9]{1}[A-Za-z0-9_.-]*@{1}[A-Za-z0-9_.-]{1,}\.[A-Za-z0-9]{1,}$/);

            if(reg.test(email) && email<255){
                return true;
            }else{
                return false;
            }
        },

        isValidPassword: function(){
            const password = this.password
            if(password.length > 6 && password.length < 32){
                return true;
            }else{
                return false;
            }
        },

        confirmPassword: function() {
            if(this.password_confirmation.length == 0){
                this.confirmClass = 'form-control';
                return false;

            }else if(this.password !== this.password_confirmation && this.password_confirmation.length > 0){
                this.confirmClass = 'form-control is-invalid';
                return false;

            }else if (this.password === this.password_confirmation){
                this.confirmClass = 'form-control is-valid';
                return true;
            }
        },
    },

    watch: {

        isValidForm(){
            this.submitStatus = this.isValidForm;
        },

        isValidName(){
            if(this.isValidName){
                this.nameClass = 'form-control is-valid';
            }else if(!this.isValidName && this.email.length > 0){
                this.nameClass = 'form-control is-invalid';
            }else{
                this.nameClass = 'form-control';
            }
        },

        isValidEmail(){
            if(this.isValidEmail){
                this.emailClass = 'form-control is-valid';
            }else if(!this.isValidEmail && this.email.length>0){
                this.emailClass = 'form-control is-invalid';
            }else{
                this.emailClass = 'form-control';
            }
        },

        isValidPassword(){
            if(this.isValidPassword){
                this.passwordClass = 'form-control is-valid';
                this.passwordError = '';
                this.isInValidPassword = false;

            }else if(!this.isValidPassword && this.password.length>0){
                this.passwordClass = 'form-control is-invalid';
                this.passwordError = 'パスワードは6文字以上32文字以内で設定してください';
                this.isInValidPassword = true;

            }else{
                this.passwordClass = 'form-control';
                this.passwordError = '';
                this.isInValidPassword = false;
            }
        }
    },
});