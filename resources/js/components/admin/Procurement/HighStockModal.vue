<template>
    <div> <!-- ✅ Wrap everything in a root div -->
      <button @click="$emit('openHighStockModal')" class="bg-white drop-shadow-lg h-full w-full dark:bg-custom-table">
          
<div class="m-5 h-[100px] text-[#73EC8B]"> 
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-full"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" /></svg>
    </div> 

        <div class="h-[160px] text-center">
          <p>High stock Materials</p><br>
          <p>{{ totalHighStocks }}</p>
          <div class="border-t-4 border-[#F7F7FD] m-5 col-span-2 flex pt-5">      
          </div>
        </div>
      </button>

  
      <div v-if="showHighStockModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 overflow-y-auto p-5">
        <div @click="$emit('closeHighStockModal')" class="absolute inset-0"></div>
        <div class=" pb-22 p-4 relative bg-white shadow-lg rounded-md w-lg w-full mt-10 mb-10  dark:bg-custom-main">
          <div class="flex justify-between">
            <div class="mt-5 text-lg ">
              <h1>High Stocks Materials</h1>
            </div>
            <button @click="$emit('closeHighStockModal')" class="mb-10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
        </button>
          </div>
          <table class="table-auto w-full border-collapse shadow-lg">
            <thead class="h-14 bg-custom-blue text-white  dark:bg-custom-table dark:border-b ">
              <tr class="bg-custom-blue text-white">
                <th class=" dark:bg-custom-table">Material Name</th>
                <th class=" dark:bg-custom-table">Stocks</th>
                <th class=" dark:bg-custom-table">Meaurement</th>
                <th class=" dark:bg-custom-table">Last updated</th>
              </tr>
            </thead>
            <tbody>
                          <!-- ✅ Display message if no materials are out of stock -->
                <tr v-if="highStocks.length === 0">
                    <td colspan="4" class="text-center px-4 py-2 border-0 text-gray-500">
                      No materials high stock
                    </td>
                </tr>
                <tr v-for="material in highStocks" :key="material.id" class="border-b bg-white hover:bg-gray-300">
                    <td class="text-center px-4 py-2 border-0">{{ material.material_name }}</td>
                    <td class="text-center px-4 py-2 border-0">{{ material.stocks }}</td>
                    <td class="text-center px-4 py-2 border-0"> {{ material.measurement_quantity }}  <span>{{ material.measurement_unit }}</span></td>
                    <td class="text-center px-4 py-2 border-0">{{ formatDate(material.updated_at || 'N/A') }}</td>
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
    props: {
        showHighStockModal: Boolean,  
    },
    data() {
        return {
            highStocks: [],
            totalHighStocks: 0,
        };
    },
    computed: {
        ...mapGetters('auth', ['user', 'userRole']),
    },
    mounted() {
        // ✅ Load from localStorage on component mount
        this.loadFromLocalStorage();
        if (this.userRole === 'procurement') {
            window.Echo.private('broadcast-high-stock')
                .listen('.highStockUpdate', (data) => {
                  console.log('✅ High stock update received:', data);
                // Log the entire object to inspect the structure
                   console.log('Data Structure:', data);
                   // Use the correct properties to assign the data
                    this.totalHighStocks = data.totalMaterials; // Match this with the property sent in the backend
                    this.highStocks = data.highStock;  // Match this with the property sent in the backend

                    // ✅ Save to localStorage when data updates
                    this.saveToLocalStorage();
                });
        }
    },
    methods: {
        // ✅ Save data to localStorage
        saveToLocalStorage() {
            localStorage.setItem('totalHighStocks', JSON.stringify(this.totalHighStocks));
            localStorage.setItem('highStocks', JSON.stringify(this.highStocks));
        },

        loadFromLocalStorage() {
            try {
                const storedTotal = localStorage.getItem('totalHighStocks');
                const storedMaterials = localStorage.getItem('highStocks');

                this.totalHighStocks = storedTotal ? JSON.parse(storedTotal) : 0;
                this.highStocks = storedMaterials ? JSON.parse(storedMaterials) : [];
            } catch (e) {
                console.error('Failed to parse localStorage data:', e);
                this.totalHighStocks = 0;
                this.highStocks = [];
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

};
</script>