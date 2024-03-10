<template>
<div class="d-flex w-100">
    <div class="row w-100">

        <div>

            <div class="bg-primary">
                <h1 class="text-center text-white py-3">{{ headerTitle }}</h1>
            </div>

            <button class="btn btn-primary mb-3" @click="aggiungiCategoria">
                <i class="fa-solid fa-square-plus mr-2 fa-lg"></i> Aggiungi categoria {{ type }}
            </button>

            <div class="bg-primary p-4">
                <table class="table table-striped bg-light rounded">
                    <tr>
                        <th style="width:15%"></th>
                        <th>
                            <label>Nome</label>
                            <input type="text" v-model="nomeAdd" class="form-control add w-50">
                        </th>
                    </tr>
                </table>
            </div>
        </div>

        <div :action="`${type}/salva`" method="post">
            <table class="table table-striped ">
                <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Nome</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(categoria, index) in categorie" :key="index">
                        <td class="d-flex align-items-center justify-content-end">

                            <div class="modal" tabindex="-1" role="dialog" id="modal">

                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                       
                                        <div class="modal-body">
                                            <p>Eliminare categoria {{ type }}  ?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button @click="elimina" type="button" data-dismiss="modal" class="btn btn-primary">Elimina</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <a @click="prepareDelete(categoria.id)" data-toggle="modal" data-target="#modal">
                                <i class="fa-solid fa-trash mx-1 text-danger mt-2"></i>
                            </a>

                        </td>
                        <td>
                            <input type="text" :value="categoria.nome" class="form-control" :name="`categorie[${categoria.id}]`">
                        </td>
                    </tr>
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary float-right px-5 mb-5">Modifica</button>
        </div>
    </div>
</div>
</template>

<script>
export default {
    name: "Categorie",
    data() {
        return {
            nomeAdd: '',
            categorie: {},
            itemTodelete: null, 
        };
    },
    props: {
        type: {
            type: String,
            required: true
        },

        headerTitle: {
            type: String,
            default: 'Categorie'
        }
    },
    methods: {
        elimina() {
            if (this.itemTodelete !== null) {
                axios.post(`/categorie/${this.type}/elimina/${this.itemTodelete}`)
                    .then(() => {
                        this.fetchData(); // Aggiorna l'elenco delle categorie
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        },

        prepareDelete(id) {
            this.itemTodelete = id;  // Imposta l'ID selezionato
        },

        fetchData() {
            axios.post(`/categorie/${this.type}/elenco`).then(response => {
                console.log(response.data)
                this.categorie = [...response.data];
            })
        },

        aggiungiCategoria() {
            axios.post(`/categorie/${this.type}/aggiungi/${this.nomeAdd}`)
                .then(response => {

                    this.nomeAdd = ''; // Resetta il campo input dopo l'invio
                })
                .catch(error => {
                    // Gestire l'errore
                    console.error(error);
                });

            this.fetchData();

        }
    },
    mounted() {
        this.fetchData();
    }
}
</script>
