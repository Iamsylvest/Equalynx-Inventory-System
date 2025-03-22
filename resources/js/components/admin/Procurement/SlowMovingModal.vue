<template>
    <div> <!-- ✅ Wrap everything in a root div -->
      <button @click="$emit('openSlowMovingModal')" class="bg-white drop-shadow-lg h-[300px] dark:bg-custom-table">
          
        <div class="m-5 h-[100px]"> 
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-full"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6 9 12.75l4.286-4.286a11.948 11.948 0 0 1 4.306 6.43l.776 2.898m0 0 3.182-5.511m-3.182 5.51-5.511-3.181" /></svg>
         </div> 

        <div class="h-[160px] text-center">
          <p>Slow Moving Materials</p><br>
          <p>{{ totalSlowMoving }}</p>
          <div class="border-t-4 border-[#F7F7FD] m-5 col-span-2 flex pt-5 p-32">      
          </div>
        </div>
      </button>

  
      <div v-if="showSlowMovingModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 overflow-y-auto p-16">
        <div @click="$emit('closeSlowMovingkModal')" class="absolute inset-0"></div>
        <div class=" pb-22 p-4 relative bg-white shadow-lg rounded-md w-lg w-full mt-10 mb-10 dark:bg-custom-main">
          <div class="flex justify-between">
            <div class="mt-5 text-lg ">
              <h1>Slow Moving Materials</h1>
            </div>
            <button @click="$emit('closeSlowMovingkModal')" class="mb-10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
        </button>
          </div>
          <table class="table-auto w-full border-collapse shadow-lg">
            <thead class="h-14">
              <tr class="bg-custom-blue text-white">
                <th  class=" dark:bg-custom-table">Material Name</th>
                <th  class=" dark:bg-custom-table">Stocks</th>
                <th  class=" dark:bg-custom-table">Meaurement</th>
                <th  class=" dark:bg-custom-table">Last updated</th>
              </tr>
            </thead>
            <tbody>
                          <!-- ✅ Display message if no materials are out of stock -->
                <tr v-if="slowMoving.length === 0">
                    <td colspan="4" class="text-center px-4 py-2 border-0 text-gray-500">
                      No materials high stock
                    </td>
                </tr>
                <tr v-for="material in slowMoving" :key="material.id" class="border-b bg-white hover:bg-gray-300">
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
        showSlowMovingModal: Boolean,
    }, 
    data() {
     return{
        slowMoving: [],
        totalSlowMoving: 0,
     }
    },
    computed:{
        ...mapGetters('auth',['user','userRole']),
    },
    mounted(){

        this.loadFromLocalStorage();
            window.Echo.private('broadcast-slow-moving')
            .listen('.slow-moving', (data) => {

                console.log('Slow moving materials Received', data);
                this.slowMoving = data.slowMovingMaterials;
                this.totalSlowMoving = data.totalSlowMoving;
                this.saveToLocalStorage();
            });
        
    },
    methods:{
            saveToLocalStorage(){
                localStorage.setItem('slowMoving', JSON.stringify(this.slowMoving));
                localStorage.setItem('totalSlowMoving', JSON.stringify(this.totalSlowMoving));

            },

            loadFromLocalStorage(){
                const storedMaterials = localStorage.getItem('slowMoving');
                const storedTotal = localStorage.getItem('totalSlowMoving');

                this.slowMoving = storedMaterials ? JSON.parse(storedMaterials) : []; // ✅ Use [] for array
                this.totalSlowMoving = storedTotal ? JSON.parse(storedTotal) : 0;
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