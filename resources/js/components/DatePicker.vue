<template>
  <div>
    <label for="selectedYear">Seleziona un anno:</label>
    <select id="selectedYear" v-model="selectedYear">
      <option value="">-- Seleziona un anno --</option>
      <!-- Utilizzo di v-for per generare le opzioni degli anni -->
      <option v-for="year in availableYears" :value="year" :key="year.id">{{ year }}</option>
    </select>

    <button @click="confirmSelection">Conferma</button>

   
  </div>
</template>

<script>
export default {
  data() {
    return {
      selectedYear: null, // Inizializza la variabile con l'anno selezionato
    };
  },
  computed: {
    availableYears() {
      // Genera un array di anni (esempio: dal 1900 al 2099)
      const startYear = 2012;
      const endYear = 2040;
      return Array.from({ length: endYear - startYear + 1 }, (_, index) =>
        String(startYear + index)
      );
    },
  },
  methods: {
    confirmSelection() {
      // Esegui azioni aggiuntive con l'anno selezionato, se necessario
      alert(`Hai selezionato l'anno: ${this.selectedYear}`);
    },
  },
};
</script>

<style scoped>
/* Stile opzionale per il componente */
label {
  font-weight: bold;
}

select {
  padding: 5px;
  margin-right: 10px;
}

button {
  padding: 5px 10px;
  background-color: #007bff;
  color: #fff;
  border: none;
  cursor: pointer;
}

button:hover {
  background-color: #0056b3;
}
</style>
