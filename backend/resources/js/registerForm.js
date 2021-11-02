import Vue from 'vue';
import {passwordRule, emailRule, nameRule} from './constants/user'

const reg = emailRule.reg;

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
        passwordError: '',
        privacy: false,
    },

    computed: {

        isValidForm: function() {
            if(this.isValidName && this.isValidEmail && this.isValidPassword && this.confirmPassword && this.privacy) {
                return true;
            }
        },

        isValidName: function() {
            if(this.name.length > 0 && this.name.length < nameRule.max){
                return true

            }else{
                return false;
            }
        },

        isValidEmail: function() {
            const email = this.email;

            const emailValid = reg.test(email);

            if(email.length == 0){
                return 3;

            }else if (!emailValid || email.length > emailRule.max){
                return false;

            }else{
                return true;
            }
        },

        isValidPassword: function(){
            const password = this.password
            if(password.length == 0){
                return 3;

            }else if(password.length < passwordRule.min || password.length > passwordRule.max){
                return false;

            }else{
                return true;
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

            }else{
                this.confirmClass = 'form-control';
                return false;
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
            switch(this.isValidEmail){
                case false:
                    this.emailClass = 'form-control is-invalid';
                    break;

                case true:
                    this.emailClass = 'form-control is-valid';
                    break;

                case 3:
                    this.emailClass = 'form-control';
                    break;
            }
        },

        isValidPassword(){
            switch(this.isValidPassword){
                case false:
                    this.passwordClass = 'form-control is-invalid';
                    this.passwordError = `パスワードは${passwordRule.min}文字以上${passwordRule.max}文字以内で設定してください`;
                    break;

                case true:
                    this.passwordClass = 'form-control is-valid';
                    this.passwordError = '';
                    break;

                case 3:
                    this.passwordClass = 'form-control';
                    this.passwordError = '';
                    break;
            }
        }
    },
});
