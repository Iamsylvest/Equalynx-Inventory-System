<template>
    <div> <!-- ✅ Wrap everything in a root div -->
      <button @click="$emit('openOutStockModal')" class="bg-white drop-shadow-lg h-[300px]  dark:bg-custom-table ">
        <div class="m-5 h-[100px] text-red-500"> 
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-full"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" /></svg>
        </div> 

  
        <div class="h-[160px] text-center">
          <p>Out stock Materials</p><br>
          <p>{{ totalOutStock }}</p>
          <div class="border-t-4 border-[#F7F7FD] m-5 col-span-2 flex pt-5 p-32">      
          </div>
        </div>
      </button>
  
      <div v-if="showOutStockModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 overflow-y-auto p-16">
        <div @click="$emit('outStockcloseModal')" class="absolute inset-0"></div>
        <div class=" pb-22 p-4 relative bg-white shadow-lg rounded-md w-lg w-full mt-10 mb-10 dark:bg-custom-main">
          <div class="flex justify-between">
            <div class="mt-5 text-lg ">
              <h1>Out Stocks Materials</h1>
            </div>
            <button @click="$emit('outStockcloseModal')" class="mb-10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
        </button>
          </div>
          <table class="table-auto w-full border-collapse shadow-lg">
            <thead class="h-14 bg-custom-blue text-white  dark:bg-custom-table dark:border-b ">
              <tr class="bg-custom-blue text-white">
                <th class=" dark:bg-custom-table">Material Name</th>
                <th  class=" dark:bg-custom-table">Stocks</th>
                <th  class=" dark:bg-custom-table">Measurement</th>
                <th  class=" dark:bg-custom-table">Last updated</th>
              </tr>
            </thead>
            <tbody>
                  <!-- ✅ Display message if no materials are out of stock -->
                  <tr v-if="outStock.length === 0">
                    <td colspan="4" class="text-center px-4 py-2 border-0 text-gray-500">
                      No materials out of stock
                    </td>
                  </tr>

                  <!-- ✅ Display materials when available -->
                  <tr 
                    v-for="material in outStock" 
                    :key="material.id" 
                    class="border-b bg-white hover:bg-gray-300"
                  >
                    <td class="text-center px-4 py-2 border-0">{{ material.material_name }}</td>
                    <td class="text-center px-4 py-2 border-0">{{ material.stocks }}</td>
                    <td class="text-center px-4 py-2 border-0">
                      {{ material.measurement_quantity }} <span>{{ material.measurement_unit }}</span>
                    </td>
                    <td class="text-center px-4 py-2 border-0">
                      {{ formatDate(material.updated_at) }}
                    </td>
                  </tr>
                </tbody>
          </table>
        </div>
      </div>
    </div>
  </template>

<script>
import { mapGetters } from 'vuex';
export default {
    props:{
        showOutStockModal: Boolean,
      },
      data(){
        return {
            outStock: [],
            totalOutStock: 0,
        }
      },
      computed:{   
           ...mapGetters('auth', [ 'user', 'userRole']),                     
      },
      mounted(){
              // ✅ Load from localStorage on component mount
              this.loadFromLocalStorage();
              if (this.userRole === 'procurement') {
                window.Echo.private('broadcast-out-stock')
                    .listen('.outStock', (data) => {
                        console.log('Out Stack data received ', data);

                        this.outStock = data.outOfStock;
                        this.totalOutStock = data.totalOutStock;
                        this.saveToLocalStorage();
                    });
              }
      },
      methods:{

        saveToLocalStorage(){
            localStorage.setItem('outStock', JSON.stringify(this.outStock));
            localStorage.setItem('totalOutStock', JSON.stringify(this.totalOutStock));
        },
        loadFromLocalStorage() {
            try {
                const storedTotal = localStorage.getItem('totalOutStock');
                const storedMaterials = localStorage.getItem('outStock');

                this.totalOutStock = storedTotal ? JSON.parse(storedTotal) : 0;
                this.outStock = storedMaterials ? JSON.parse(storedMaterials) : [];
            } catch (e) {
                console.error('Failed to parse localStorage data:', e);
                this.totalOutStock = 0;
                this.outStock = [];
            }
        },
        formatDate(date) {
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    },

      },

    }
</script>