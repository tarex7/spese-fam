<template>
<div>
    <div class="row">
        <div class=" bg-primary">
            <h1 class="text-center text-white my-3">Elenco {{type}}</h1>
        </div>

        <div class="col-12 d-flex  justify-content-end border my-3 ">
            <form :action="`${type}/filtra`" method="get">
                <div class="d-flex float-right my-4 ">

                    <select class="form-control mx-1" v-model="localyear" @change="filtra">
                        <option v-for="(label, value) in years_opt" :key="value" :value="value">
                            {{ label  }}
                        </option>
                    </select>
                </div>

            </form>

        </div>
        <div class="col-12">

            <!-- <apexchart :key="graphKey" width="100%" height="300px" type="bar" :options="options" :series="series"></apexchart> -->
            <apexchart-componentt :year="localyear" />
            <table class="table table-striped">

                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Spesa</th>
                        <th scope="col">GEN</th>
                        <th scope="col">FEB</th>
                        <th scope="col">MAR</th>
                        <th scope="col">APR</th>
                        <th scope="col">MAG</th>
                        <th scope="col">GIU</th>
                        <th scope="col">LUG</th>
                        <th scope="col">AGO</th>
                        <th scope="col">SET</th>
                        <th scope="col">OTT</th>
                        <th scope="col">NOV</th>
                        <th scope="col">DIC</th>
                        <th scope="col" class="bg-secondary">TOT</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="(importiMensili, categoria) in dati" :key="categoria">
                        <td class="font-weight-bold bg-secondary text-white">{{ categoria }}</td>
                        <td v-for="mese in Array.from({ length: 12 }, (_, i) => i + 1)" :key="mese" :class="{ 'text-muted': importiMensili[mese] === 0 }">
                            {{ importiMensili.importi_mensili[mese].toFixed(2) }} €
                        </td>
                        <td class="font-weight-bold bg-warning text-dark">
                            {{ importiMensili.total.toFixed(2) }} €
                        </td>
                    </tr>

                    <tr class="font-weight-bold bg-secondary text-white">
                        <td class="font-weight-bold bg-secondary text-white">Totale</td>

                        <td class="tots" v-for=" (tot,idx) in totPerMese" :key="idx">
                            {{ tot }} €
                        </td>

                        <td class="font-weight-bold bg-secondary text-white">{{sommaArray}} €</td>

                    </tr>
                </tbody>

            </table>
        </div>
    </div>
</div>
</template>

<script>
import ApexchartComponentt from './ApexchartComponentt.vue'
export default {
    components: {
        ApexchartComponentt
    },
    data() {
        return {
            dati: {},
            graphKey: 0,
            localyear: this.anno,
            totPerMese: [],

            years: this.years_opt,

        }
    },
    methods: {
        filtra() {
            this.graphKey++;

            axios.get(`${this.getdataurl}/${this.localyear}`)
                .then(
                    res => {
                        this.dati = res.data;

                        this.totalRecords = res.data.total

                    }
                )

            axios.get(`/spese/spesemensili/${this.localyear}`).then(
                res => {

                    this.totPerMese = res.data

                }
            )
        },
        formatCurrency(value) {

            return value;
        },

    },
    watch: {
        localyear(newval) {
            this.localyear = newval
            this.filtra()
            //console.log(this.localyear)
        }
    },
    computed: {
        sommaArray() {
            let somma = 0;
            for (let mese of this.totPerMese) {
                somma += Number(mese);
            }
            return somma.toFixed(2);

        }
    },
    props: {
        years_opt: [Object],
        getdataurl: [String],
        type: [String],
        anno: [String, Number],
        year: [String, Number]
    },
    beforeMount() {
        this.filtra()

    },
    mounted() {
        const tots = document.querySelectorAll('.tots');
        console.log(tots)
    }
}
</script>

<style>

    </style>
