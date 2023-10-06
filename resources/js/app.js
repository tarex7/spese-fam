/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

/*require('./bootstrap');

window.Vue = require('vue');

Vue.component('example-component', require('./components/ExampleComponent.vue').default);*/



import Vue from 'vue';
import ExampleComponent from './components/ExampleComponent.vue';
import DataTableComponent from './components/DataTableComponent.vue';

import 'bootstrap/dist/css/bootstrap.css'; // Importa il file CSS di Bootstrap
import 'bootstrap/dist/js/bootstrap.bundle'; // Importa il file JavaScript di Bootstrap

Vue.component('example-component', ExampleComponent);
Vue.component('datatable-component', DataTableComponent);

const app = new Vue({
    el: '#app',
    mounted() {
        console.log('App is working')
        const spese_div = document.getElementById('spese_div')
    }
}
);


if (document.getElementById('spese') != null) {
    const spese = new Vue({
        el: '#spese',
        data() {
            return {
               
            }
        },
        props: ['catopt'],
        beforeMount() {
           
        },
        mounted() {
            const addBtn = document.getElementById('addBtn')
            const add = document.querySelectorAll('.add')
            addBtn.addEventListener('click', () => {

                add.forEach(a => {
                    console.log(a)
                })
                
            })
        },
        methods: {
           
        }
    }
    );
}







