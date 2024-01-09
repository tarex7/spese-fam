<template>
<div>

    <apexchart ref="apexChart" width="100%" height="300px" type="bar" :options="options" :series="series"></apexchart>

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
            anno: this.year,

        }
    },
    props: {
        years_opt: [Object],
        year: [String, Number],
    },
    methods: {
        getData() {

            axios.get(`/spese/spesemensili/${this.year}`).then(
                res => {
                    console.log( res.data)
                    this.anno = this.year
                    this.series[0].data = res.data
                }
            )
        },
        updateChart(newSeries) {
            if (this.$refs.apexChart) {
                this.$refs.apexChart.updateSeries(newSeries, true);
            }
        }
    },
    watch: {
        year(newYear) {

            this.anno = newYear;
            this.getData();

        },
        series: {
            deep: true,
            handler(newSeries) {
                console.log('newseiers', newSeries)
                this.updateChart(newSeries);
            }
        }
    },

    beforeMount() {
        this.getData()

    },
    mounted() {

        // this.$refs.apexChart = this.$el.querySelector('.apexcharts-canvas');
    }
}
</script>
