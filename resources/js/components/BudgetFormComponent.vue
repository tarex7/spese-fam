<template>
<div>
    <div class="row ">
        <div class="col-5"></div>

        <div class=" bg-primary">
            <h1 class="text-center text-white my-3">{{title}}</h1>
        </div>

        <!-- FILTRA -->
        <div class="col-12 d-flex  justify-content-end border my-3 ">
            <form :action="`${type}/filtra`" method="get">
                <div class="d-flex float-right my-4">

                    <a class="btn btn-primary  mr-5" href="">Elenco</a>

                    <select class="form-control mx-1" v-model="anno" @change="filtra">
                        <option v-for="(label, value) in years_opt" :key="value" :value="value">
                            {{ label  }}
                        </option>
                    </select>

                    <select class="form-control mx-1" v-model="mese" @change="filtra">
                        <option>--Seleziona--</option>
                        <option v-for="(label, value) in months_opt" :key="value" :value="value">
                            {{ label  }}
                        </option>
                    </select>

                    <!-- <a class="btn btn-success  mx-2" @click.prevent="filtra">Filtra</a>
                    <a class="btn btn-danger  mx-2" href="">Rimuovi</a> -->
                </div>
            </form>

        </div>

        <!-- AGGIUNGI -->
        <div>
            <form :action="`${type}/aggiungi`" method="get">

                <button class="btn btn-primary mb-3" type="submit">
                    <i class="fa-solid fa-square-plus mr-2 fa-lg"></i> Aggiungi spesa
                </button>

                <div class="border bg-primary p-3 mb-3 rounded">
                    <table class="table table-striped bg-light rounded ">
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">Categoria</th>
                                <th scope="col">Data</th>
                                <th scope="col">Importo</th>
                                <th scope="col">Tipologia</th>
                            </tr>
                        </thead>
                        <tr>
                            <td></td>
                            <td>
                                <select class="form-control add" :name="`categorie${type}_add`">
                                    <option value="0">--Seleziona--</option>
                                    <option v-for="(label, value) in cat_opt" :key="value" :value="value">
                                        {{ label }}
                                    </option>
                                </select>
                            </td>

                            <td>
                                <input type="date" class="form-control add" name="data_add">
                            </td>

                            <td>
                                <input type="number" class="form-control add" step="0.01" min="0.01" placeholder="0.00" name="importo_add">
                            </td>

                            <td>
                                <select class="form-control add" name="tipologia_add">
                                    <option value="0">--Seleziona--</option>

                                    <option v-for="(option, key) in tip_opt" :key="key" :value="key">{{ option }}</option>
                                </select>
                            </td>

                        </tr>
                    </table>
                </div>

            </form>
        </div>

        <!-- TABLE -->
        <form :action="`${type}/salva`" method="get">
            <input type="hidden" v-model="anno" name="anno_sel">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th scope="col">Categoria</th>
                        <th scope="col">Data</th>
                        <th scope="col">Importo</th>
                        <th scope="col">Tipologia</th>
                    </tr>
                </thead>
                <tbody :id="`${type}`">
                    <tr>
                        <th></th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                    </tr>
                    <tr v-for="s in dati" :key="s.id" :id="s.id === s.id ? 'nome_add' : ''">

                        <td class="d-flex align-items-center justify-content-center border-0">
                            <a @click="elimina" :href="`/${type}/elimina/${s.id}`">
                                <i class="fa-solid fa-trash mx-1 text-danger mt-2"></i>
                            </a>
                        </td>

                        <td class="border-0">
                            <select v-model="s[selectedCategory]" class="form-control" :name="`${type}[${s.id}][categorie${type}]`">
                                <option v-for="(label, value) in cat_opt" :key="value" :value="value">{{ label }}</option>
                            </select>
                        </td>

                        <td class="border-0">
                            <input type="date" class="form-control" v-model="s.data" :name="`${type}[${s.id}][data]`">
                        </td>

                        <td class="border-0">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">€</span>
                                </div>
                                <input type="number" class="form-control" v-model="s.importo" step="0.01" min="0.00" :name="`${type}[${s.id}][importo]`">
                            </div>
                        </td>

                        <td class="border-0">
                            <select v-model="s.tipologia_id" class="form-control" :name="`${type}[${s.id}][tipologia]`">
                                <option v-for="(label, value) in tip_opt" :key="value" :value="value">{{ label }}</option>
                            </select>
                        </td>
                    </tr>

                </tbody>
            </table>

            <table class="table table-striped bg-light rounded ">
                <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col">Totale</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tr>
                    <td></td>
                    <td style="width:30%">

                    </td>

                    <td style="width:22%">

                    </td>

                    <td>
                        <div class="input-group">

                            <div class="input-group-prepend">
                                <span class="input-group-text">€</span>
                            </div>
                            <input v-model="totale" type="number" class="form-control add" step="0.01" min="0.01" placeholder="0.00">
                        </div>
                    </td>

                    <td style="width:13%">

                    </td>

                </tr>
            </table>
            <button type="submit" class="btn btn-primary float-right mr-5 px-5 mb-5">
                Salva
            </button>

        </form>

        <pagination @paginate="handlePageChange" v-model="currentPage" :records="totalRecords" :per-page="itemsPerPage">
        </pagination>

    </div>
</div>
</template>

<script>
import ConfirmModal from './ConfirmModal.vue';
//import Pagination from 'vue-pagination-2';

export default {
    components: {
        ConfirmModal
    },

    props: {
        title: [String],
        years_opt: [Object],
        months_opt: [Object],
        cat_opt: [Array, Object],
        tip_opt: [Array, Object],
        getdataurl: [String],
        delete: [String],
        type: [String],
        vmodel: [String],
        anno: [String, Number]

    },
    data() {
        return {
            categorie_add: "--Seleziona--",
            year: this.anno,
            mese: new Date().getMonth() + 1,

            dati: {},
            totale: 0,
            currentPage: 1,
            itemsPerPage: 10,
            totalRecords: 0,

        }
    },
    computed: {
        selectedCategory() {
            return 'categorie' + this.type + '_id';
        }
    },

    watch: {
        dati: {
            handler(newValue) {
                this.calcolaTotale();
            },
            deep: true // Imposta su true per osservare le modifiche all'interno degli oggetti dell'array
        }
    },
    methods: {
        filtra() {
            const formData = {
                mese: this.mese,
                anno: this.anno,
                page: this.currentPage,
                limit: this.itemsPerPage,
            }
            axios.post(this.getdataurl, formData)
                .then(
                    res => {
                        this.dati = res.data.data;
                        console.log(this.dati)
                        this.totalRecords = res.data.total
                    }
                )
        },
        calcolaTotale() {
            let somma = 0;
            this.dati.forEach(spesa => {
                somma += parseFloat(spesa.importo);
            });
            this.totale = somma.toFixed(2); // Arrotonda a due cifre decimali
        },
        handlePageChange(page) {
            console.log()
            this.currentPage = page;
            this.filtra(); // Richiama il metodo di filtraggio per ottenere i dati della nuova pagina
        },
        elimina(id, e) {
            if (!confirm('eliminare?')) {
                e.preventDefault();

            }
        }
    },
    beforeMount() {
        this.filtra()
    },
    mounted() {
        console.log(this.anno)
    }
}
</script>

<style>

</style>
