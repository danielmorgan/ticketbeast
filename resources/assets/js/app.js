import Vue from 'vue';
import axios from 'axios';

axios.defaults.headers.common = {
    'X-CSRF-TOKEN': window.Laravel.csrfToken,
    'X-Requested-With': 'XMLHttpRequest'
};

Vue.component('checkout', require('./components/Checkout.vue'));

const app = new Vue({
    el: '#app'
});
