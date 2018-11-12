
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import Gate from "./Gate";
Vue.prototype.$gate = new Gate(window.user);

import swal from 'sweetalert2';
window.swal = swal;

const toast = swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

window.toast = toast;

import VueProgressBar from 'vue-progressbar';
const options = {
    color: '#bffaf3',
    failedColor: '#874b4b',
    thickness: '5px',
    transition: {
      speed: '0.2s',
      opacity: '0.6s',
      termination: 300
    },
    autoRevert: true,
    location: 'top',
    inverse: false
}
  
Vue.use(VueProgressBar, options)

import moment from 'moment';

import { Form, HasError, AlertError } from 'vform';

window.Form = Form;
Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)

import VueRouter from 'vue-router'
Vue.use(VueRouter)

let routes = [
    { path: '/dashboard', component: require('./components/Dashboard.vue') },
    { path: '/users', component: require('./components/Users.vue') },
    { path: '/profile', component: require('./components/Profile.vue') },
    { path: '/tasks', component: require('./components/Tasks.vue') }, 
    { path: '/taskdetails/:taskId', name: 'taskdetails', component: require('./components/TasksDetail.vue') }
]

const router = new VueRouter({
    //mode: 'history',
    routes // short for `routes: routes`
})

Vue.filter('upText', function(text){
    return text.charAt(0).toUpperCase() + text.slice(1);
});

Vue.filter('formatDate', function(myDate){
    if (myDate) {
        return moment(myDate).format('DD-MM-YYYY');
    }
});

Vue.filter('formatState', function(myState){
    if (myState == 1) {
        return 'Finalizada';
    } else if (myState == 0) {
        return 'Pendiente';
    }
});

window.Fire = new Vue();

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

/*Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue')
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue')
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue')
);

Vue.component('example-component', require('./components/ExampleComponent.vue'));*/

Vue.component(
    'not-found',
    require('./components/NotFound.vue')
);

const app = new Vue({
    el: '#app',
    router
});
