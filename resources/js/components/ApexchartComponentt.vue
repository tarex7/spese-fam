<template>
<div>

    <apexchart class="ms-3" ref="apexChart" width="100%" height="350px" :type="chartType" :options="options" :series="series"></apexchart>

</div>
</template>

<script>
export default {
    data() {
        return {
            options: {
                dataLabels: {
                    enabled: false,
                    style: {
                        fontSize: '12px', // Riduci la dimensione del font delle etichette
                        colors: ["#000"]
                    },
                    
                },
                chart: {
                    type: "bar", // Default chart type
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            position: 'top', // Posizione le etichette sopra le barre
                        }
                    }
                },
                xaxis: {

                    categories: ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'],
                    type: 'category',
                    categories: [],
                    tickAmount: undefined,
                    tickPlacement: 'on',
                    min: undefined,
                    max: undefined,
                    range: undefined,
                    floating: false,
                    decimalsInFloat: undefined,
                    overwriteCategories: undefined,
                    position: 'bottom',
                    labels: {

                        show: true,
                        rotate: -45,
                        rotateAlways: false,
                        hideOverlappingLabels: true,
                        showDuplicates: false,
                        trim: false,
                        minHeight: undefined,
                        maxHeight: 120,
                        style: {
                            colors: [],
                            fontSize: '12px',
                            fontFamily: 'Helvetica, Arial, sans-serif',
                            fontWeight: 400,
                            cssClass: 'apexcharts-xaxis-label',
                        },
                        offsetX: 0,
                        offsetY: 0,
                        format: undefined,
                        formatter: undefined,
                        datetimeUTC: true,
                        datetimeFormatter: {
                            year: 'yyyy',
                            month: "MMM 'yy",
                            day: 'dd MMM',
                            hour: 'HH:mm',
                        },
                    },
                    group: {
                        groups: [],
                        style: {
                            colors: [],
                            fontSize: '12px',
                            fontWeight: 400,
                            fontFamily: undefined,
                            cssClass: ''
                        }
                    },
                    axisBorder: {
                        show: true,
                        color: '#78909C',
                        height: 1,
                        width: '100%',
                        offsetX: 0,
                        offsetY: 0
                    },
                    axisTicks: {
                        show: true,
                        borderType: 'solid',
                        color: '#78909C',
                        height: 6,
                        offsetX: 0,
                        offsetY: 0
                    },

                    title: {
                        text: undefined,
                        offsetX: 0,
                        offsetY: 0,
                        style: {
                            color: undefined,
                            fontSize: '12px',
                            fontFamily: 'Helvetica, Arial, sans-serif',
                            fontWeight: 600,
                            cssClass: 'apexcharts-xaxis-title',
                        },
                    },
                    crosshairs: {
                        show: true,
                        width: 1,
                        position: 'back',
                        opacity: 0.9,
                        stroke: {
                            color: '#b6b6b6',
                            width: 0,
                            dashArray: 0,
                        },
                        fill: {
                            type: 'solid',
                            color: '#B1B9C4',
                            gradient: {
                                colorFrom: '#D8E3F0',
                                colorTo: '#BED1E6',
                                stops: [0, 100],
                                opacityFrom: 0.4,
                                opacityTo: 0.5,
                            },
                        },
                        dropShadow: {
                            enabled: false,
                            top: 0,
                            left: 0,
                            blur: 1,
                            opacity: 0.4,
                        },
                    },
                    tooltip: {
                        enabled: true,
                        formatter: undefined,
                        offsetY: 0,
                        style: {
                            fontSize: 0,
                            fontFamily: 0,
                        },
                    },
                },
            },
            series: [{
                name: `spese ${this.year[0]}`,
                data: []
            }, {
                name: ``,
                data: []
            }],
            years: this.years_opt,
            anno: this.year,

        }
    },
    props: {
        years_opt: [Object],
        year: [Array],
        type: [String],
        chartType: [String]
    },
    methods: {
        getData() {
            console.log(this.year)
            // Itera sui anni selezionati e ottieni i dati per ciascuno
            this.year.forEach(year => {
                axios.get(`/${this.type}/${this.type}mensili/${year}`).then(res => {
                    // Assumi che l'indice 0 sia per il primo anno e l'indice 1 per il secondo
                    const index = this.year.indexOf(year);
                    this.series[index].data = res.data;
                });
            });
        },
        updateChartType(newType) {
            this.options.chart.type = newType;
            this.updateChart();
        },
        updateChart(newSeries) {
            if (this.$refs.apexChart) {
                this.$refs.apexChart.updateSeries(newSeries, true);
            }
        }
    },
    watch: {
        year(newYear) {
            this.series[1].name = 'spese ' + this.year[1]
            console.log('this.year', this.year)
            this.anno = this.year[0];
            console.log(this.anno)
            this.getData();

        },

        series: {
            deep: true,
            handler(newSeries) {
                console.log('newSeries', newSeries)
                this.updateChart(newSeries);
            }
        },

        chartType(newType) {
            this.updateChartType(newType);
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
