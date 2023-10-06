<template>
<div>
 
  <table class="table table-striped">
            <thead>
                <tr>
                    <th></th>
                    <th scope="col">Data</th>
                    <th scope="col">Importo</th>
                    <th scope="col">Categoria</th>
                    <th scope="col">Tipologia</th>
                </tr>
            </thead>
            <tbody id="spese_div">
              
              
                    <tr v-for="s in spese" :key="s.id">
                        <td> <i class="fa-solid fa-pencil text-secondary"></i>
                            <i class="fa-solid fa-trash mx-1 text-danger"></i>
                        </td>
                      
                        <td>
                        <input class=form-control type="date" v-model="s.data" >
                        </td>
                        <td><input class=form-control type="text" :value="nome"></td>
                        <td>
                          <select class="form-control">
                           <option v-for="c in cat_opt" :key="c.id">{{c}}</option>
                          </select>
                        </td>
                        <td>
                         <select v-model="s.tipologia" class="form-control">
                           <option  v-for="t in tip_opt" :key="t.key">{{t}}</option>
                          </select>
                        </td>
                    </tr>
               

            </tbody>
        </table>
</div>
</template>

<script>
export default {
    data() {
        return {
           spese:{},
           nome:'tore',
           messaggio:''
          
        }
    },
    props: ['info','cat_options','tip_options'],
    beforeMount() {
         this.spese = JSON.parse(this.info)
         this.cat_opt = JSON.parse(this.cat_options)
         this.tip_opt = JSON.parse(this.tip_options)
    },
        mounted() {
            console.log('Data table mounted.')
            console.log(this.spese)
        },
        methods: {
           add() {
            console.log('added')
            this.spese.push({
              data: '',
              importo: null, // Puoi inizializzare a null o a un altro valore predefinito
              categoria: '',
              tipologia: ''
            });
          }
        }
    }
</script>

<style>

</style>