
const catOptAttribute = document.getElementById('spese').getAttribute('data-catopt');
const add = document.getElementById('add')
add.addEventListener('click', () => {
         console.log('sono qua')
         const div = document.createElement('tr');

         div.innerHTML = `
           
                 <td>
                     <i class="fa-solid fa-pencil text-secondary"></i>
                     <i class="fa-solid fa-trash mx-1 text-danger"></i>
                 </td>
                  <input type="hidden" name="spese_added[]" value="" class="form-control">
                 <td><input type="date" name="data_added[]" value="" class="form-control"></td>
                 <td><input type="number" name="importo_added[]" value="" class="form-control"></td>
                 <td>
                     <select name="categorie_added[]" selected="1" class="form-control">
                       
                        <option v-for="cat in catOptAttribute">${catOptAttribute}</option>
                       
                     </select>
                 </td>
                 <td>
                     <select name="tipologia_added[]" class="form-control">
                         <!-- Opzioni della select -->
                     </select>
                 </td>
           
         `;
         spese_div.appendChild(div);
     });