import Vue from "vue";
import VueRouter from "vue-router";
import Vuex from "vuex";
import VueEvents from 'vue-events';

import ElementUI from 'element-ui';
import locale from 'element-ui/lib/locale/lang/en'

import {routes} from "./routes";

import MainApp from "./components/MainApp.vue";
import JsonCSV from 'vue-json-csv';



require('./bootstrap');

window.Vue = Vue;

Vue.use(ElementUI, { locale });

Vue.use(VueEvents);
Vue.use(VueRouter);
Vue.use(Vuex);
Vue.component('downloadCsv', JsonCSV)

// const store = new Vuex.Store(StoreDate);
const router = new VueRouter({
    routes,
    mode: 'history'
});


const app = new Vue({
    el: '#app',
    router,
    components:{
        MainApp
    }
});
