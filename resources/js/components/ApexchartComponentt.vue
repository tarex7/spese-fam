<template>
<div>
    <!-- <select class="form-control mx-1" v-model="year">
        <option v-for="(label, value) in years_opt" :key="value" :value="value">
            {{ label  }}
        </option>
    </select> -->
    <h1>{{anno}}</h1>
    <apexchart width="100%" height="300px" type="bar" :options="options" :series="series"></apexchart>

</div>
</template>

<script>
export default {
    data() {
        return {
            options: {
                chart: {
                    id: 'vuechart-example'
                },
                xaxis: {
                    categories: ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic']

                }
            },
            series: [{
                name: 'spese',
                data: []
            }],
            years: this.years_opt,
            anno: this.year

        }
    },
    props: {
        years_opt: [Object],
        year: [String, Number]
    },
    methods: {
        getData() {
            console.log('okapex')
            axios.get(`/spese/spesemensili/${this.year}`).then(
                res => {
                    this.series[0].data = []
                    this.anno = this.year
                    console.log(`/spese/spesemensili/${this.year}`)
                    this.series[0].data = res.data
                    // console.log(this.series[0].data)
                }
            )
        }
    },
    watch: {
        year() {
            console.log(`ora il valore di anno è ${this.year}`)
            this.getData()
            console.log('dati del grafico', this.series[0].data)

        }
    },

    beforeMount() {
        this.getData()

    },
    mounted() {
        //console.log(this.years)
    }
}
</script>
