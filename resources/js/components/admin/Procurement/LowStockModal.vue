<template>
    <div> <!-- ✅ Wrap everything in a root div -->
      <button @click="$emit('openLowStockModal')" class="bg-white drop-shadow-lg h-full w-full dark:bg-custom-table" >
        <div class="m-5 h-[100px] text-red-500"> 
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-full animate-customPulse"> 
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
          </svg>
        </div> 
  
        <div class="h-[160px] text-center   ">
          <p>Low stock Materials</p><br>
          <p>{{ totalLowStock }}</p>
          <div class="border-t-4  border-[#F7F7FD] m-5 col-span-2 flex pt-5">      
          </div>
        </div>
      </button>
  
      <div v-if="showLowStockModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 w-full z-50 overflow-y-auto p-5">
        <div @click="$emit('lowStockcloseModal')" class="absolute inset-0"></div>
        <div class="pb-22 p-4 relative bg-white shadow-lg rounded-md  min-w-[350px] mt-10 mb-10 dark:bg-custom-main"> <!--MODAL-->
          <div class="flex justify-between">
            <div class="mt-5 text-lg ">
              <h1>Low Stocks Materials</h1>
            </div>
            <button @click="$emit('lowStockcloseModal')" class="mb-10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
        </button>
          </div>
          <table class="table-auto w-full border-collapse shadow-lg">
            <thead class="h-14 bg-custom-blue text-white  dark:bg-custom-table dark:border-b ">
              <tr class="bg-custom-blue text-white">
                <th class="dark:bg-custom-table ">Material Name</th>
                <th class="dark:bg-custom-table">Stocks</th>
                <th class="dark:bg-custom-table">Measurement</th>
                <th class="dark:bg-custom-table">Last updated</th>
              </tr>
            </thead>
            <tbody>
                          <!-- ✅ Display message if no materials are out of stock -->
               <tr v-if="lowStockMaterials.length === 0">
                    <td colspan="4" class="text-center px-4 py-2 border-0 text-gray-500">
                      No materials low stock
                    </td>
               </tr>
              <tr v-for="material in lowStockMaterials" :key="material.id" class="border-b bg-white hover:bg-gray-300">
                <td class="text-center px-4 py-2 border-0">{{ material.material_name }} </td>
                <td class="text-center px-4 py-2 border-0">{{ material.stocks }}</td>
                <td class="text-center px-4 py-2 border-0">{{ material.measurement_quantity}} <span>{{ material.measurement_unit }}</span></td>
                <td class="text-center px-4 py-2 border-0">{{ formatDate(material.updated_at) }}</td>
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
        showLowStockModal: Boolean,  
    },
    data() {
        return {
            lowStockMaterials: [],
            totalLowStock: 0,
        };
    },
    computed: {
        ...mapGetters('auth', ['user', 'userRole']),
    },
    mounted() {
        // ✅ Load from localStorage on component mount
        this.loadFromLocalStorage();

        if (this.userRole === 'procurement') {
            window.Echo.private('low-stock-dashboard')
                .listen('.lowStockUpdated', (data) => {
                    console.log('Received low stock update', data);

                    this.totalLowStock = data.totalLowStock;
                    this.lowStockMaterials = data.lowStockMaterials;

                    // ✅ Save to localStorage when data updates
                    this.saveToLocalStorage();
                });
        }
    },
    methods: {
        // ✅ Save data to localStorage
        saveToLocalStorage() {
            localStorage.setItem('totalLowStock', JSON.stringify(this.totalLowStock));
            localStorage.setItem('lowStockMaterials', JSON.stringify(this.lowStockMaterials));
        },

        // ✅ Load data from localStorage
        loadFromLocalStorage() {
            const storedTotal = localStorage.getItem('totalLowStock');
            const storedMaterials = localStorage.getItem('lowStockMaterials');

            if (storedTotal) {
                this.totalLowStock = JSON.parse(storedTotal);
            }
            if (storedMaterials) {
                this.lowStockMaterials = JSON.parse(storedMaterials);
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