/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))




import DatePicker from './components/DatePicker.vue'
import ExampleComponent from './components/ExampleComponent.vue'
import BudgetForm from './components/BudgetFormComponent.vue'
import Pagination from 'vue-pagination-2';

Vue.component('example-component', ExampleComponent);


Vue.component('Datepicker', DatePicker);
Vue.component('budget-form-component', BudgetForm);
Vue.component('pagination', Pagination);



/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

if (!document.getElementById('spese')) {
    const app = new Vue({

        el: '#app',
        mounted() {
            console.log('App is working');
        },
        methods: {
            elimina(e) {

                if (!confirm('Eliminare questa spesa? 🐱')) {
                    e.preventDefault();
                }
            },
            sendMonth() {
                console.log('Valore selezionato:', this.meseSelezionato)
            }
        }
    });
}

if (document.getElementById('spese')) {
    const spese = new Vue({
        el: '#spese',

        data() {
            return {
                categoria_id: this.categoria_id,
                meseSelezionato: null
            }
        },
        mounted() {

            document.addEventListener("DOMContentLoaded", function () {
                const elementToFocus = document.getElementById('nome_add');

                console.log(elementToFocus)
                if (elementToFocus) {
                    elementToFocus.scrollIntoView();
                    elementToFocus.style.backgroundColor = "#ccc"
                }
                console.log("La pagina è stata completamente caricata.");
            });
            console.log('Spese is working')


        },

        methods: {
            elimina(e) {

                if (!confirm('Eliminare questa spesa? 🐱')) {
                    e.preventDefault();
                }
            },
            sendMonth() {
                console.log('Valore selezionato:', this.meseSelezionato)
            }
        }
    });
}
