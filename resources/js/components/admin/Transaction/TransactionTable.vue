<template>
    <div class="mt-10 font-roboto">
      <!-- Main container -->
      <div class="flex flex-wrap items-center justify-between relative top-[-10px] w-full">
        <!-- Left Section: Buttons -->
        <div class="flex space-x-4">
          <button
            class="p-3 px-12 text-md font-bold border-b-2 shadow-md rounded-md focus:outline-none w-full sm:w-auto"
            @click="showTable = 'delivery'"
            :class="{
              'bg-custom-blue text-white': showTable === 'delivery',
              'hover:bg-custom-blue hover:text-white': showTable !== 'delivery'
            }"
          >
            Delivery Receipt
          </button>
  
          <button
            class="p-3 px-12 text-md font-bold border-b-2 shadow-md rounded-md focus:outline-none w-full sm:w-auto"
            @click="showTable = 'return'"
            :class="{
              'bg-custom-blue text-white': showTable === 'return',
              'hover:bg-custom-blue hover:text-white': showTable !== 'return'
            }"
          >
            Return Receipt
          </button>
        </div>
        <div>
            <addDR @addedDR="handleDRadded"  @closeModal="showDrmodal = false"  />
        </div>
      </div>
  
      <div>
        <table v-if="showTable === 'delivery'" class="table-auto w-full border-collapse mt-1 shadow-lg">
          <thead class="h-14 bg-gray-100">
            <tr class="bg-custom-blue text-white">
              <th class="px-6 py-4 font-bold text-center">Date Added</th>
              <th class="px-6 py-4 font-bold text-center">DR#</th>
              <th class="px-6 py-4 font-bold text-center">Name</th>
              <th class="px-6 py-4 font-bold text-center">Project Name</th>
              <th class="px-6 py-4 font-bold text-center">Approved by</th>
              <th class="px-6 py-4 font-bold text-center">Status</th>
              <th class="px-4 py-2 border-0 font-bold text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in drs" :key="index" 
            class="border-b hover:bg-gray-200"
            >
              <td class="px-6 py-4 text-center">{{ formatDate(item.created_at) }}</td>

              <td class="px-6 py-4 text-center">
                <button @click= "viewDr(item.id)" class="hover:underline custom-bg-blue ">
                  {{ item.dr_number }}
                </button>
              </td>
              <td class="px-6 py-4 text-center">{{ item.name }}</td>
              <td class="px-6 py-4 text-center">{{ item.project_name }}</td>
              <td class="px-6 py-4 text-center">{{ item.approved_by }}</td>
              <td class="px-6 py-4 text-center">{{ item.status }}</td>
              <td class="text-center px-4 py-2 border-0">
                <button @click="deleteDr(item.id)"  class="text-gray-500 hover:underline">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
              </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div>
        <table v-if="showTable === 'return'" class="table-auto w-full border-collapse mt-1 shadow-lg">
          <thead class="h-14 bg-gray-100">
            <tr class="bg-custom-blue text-white">
              <th class="px-6 py-4 font-bold text-center">Date</th>
              <th class="px-6 py-4 font-bold text-center">DR#</th>
              <th class="px-6 py-4 font-bold text-center">Name</th>
              <th class="px-6 py-4 font-bold text-center">Project Name</th>
              <th class="px-6 py-4 font-bold text-center">Approved by</th>
              <th class="px-6 py-4 font-bold text-center">Status</th>
              <th class="px-4 py-2 border-0 font-bold text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in drs" :key="index" class="bg-white border-b">
              <td class="px-6 py-4 text-center">{{  }}</td>
              <td class="px-6 py-4 text-center">{{  }}</td>
              <td class="px-6 py-4 text-center">{{ }}</td>
              <td class="px-6 py-4 text-center">{{ }}</td>
              <td class="px-6 py-4 text-center">{{ }}</td>
              <td class="px-6 py-4 text-center">{{  }}</td>
              <td class="text-center px-4 py-2 border-0">
                <button class="text-gray-500 hover:text-custom-blue hover:underline w-full sm:w-auto">
                  <!-- Icon here -->
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <viewDr 
          v-if="showViewDrModal" 
          :show="showViewDrModal" 
          :item="selectedDR" 
          :materials="selectedmaterials"  
          @close="showViewDrModal = false" 
        />
    </div>
</template>
  
<script>
import TransactionNotification from '@/components/admin/Transaction/TransactionNotification.vue';
import TransactionProfile from '@/components/admin/Transaction/TransactionProfile.vue';
import addDR from '@/components/admin/Transaction/addDR.vue';
import viewDr from '@/components/admin/Transaction/viewDr.vue';
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    components: {
        TransactionNotification,
        TransactionProfile,
        addDR,
        viewDr,
  
    },
    data() {
        return {
            showTable: 'delivery', // Default value
            showViewDrModal: false, // Control modal visibility
            selectedDR: null, // ✅ Initialize as null
            selectedmaterials: [], // ✅ Initialize as empty array
            drs: [], // Fixed naming for clarity
            
        };
    },
    mounted() {
        this.fetchDRs(); // Fetch data when the component loads
    },
    methods: {
        async fetchDRs() {
            try {
                const response = await axios.get("/api/Dr");
                this.drs = response.data;
            } catch (error) {
                console.error("Error Fetching DRs:", error);
            }
        },

        handleDRadded(newDr) {
            this.drs.push(newDr);
            this.fetchDRs(); // Refetch all DRs to ensure consistency
        },

        formatDate(date) {
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    },
    async viewDr(id) {
      try {
        const response = await axios.get(`/api/Dr/${id}`);
        console.log("API Response:", response.data); // ✅ Check if materials exist

        if (response.data) {
          this.selectedDR = response.data.item || {}; // ✅ Correct property
          this.selectedmaterials = response.data.materials || []; // ✅ Correct property
          this.showViewDrModal = true; // ✅ Open the modal
        } else {
          console.error("No data received from API");
        }
      } catch (error) {
        console.error("Error fetching delivery receipt:", error);
      }
    },
    deleteDr(deliveryReceiptId){
        Swal.fire({
          title: "Are you sure?",
          text: "You won't be able to revert this!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#d33",
          cancelButtonColor: "#3085d6",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
          if (result.isConfirmed) {
            axios.delete(`/api/Dr/${deliveryReceiptId}`).then(() => {
                // ✅ Fetch updated list after deletion
                this.fetchDRs();
                   // Update the frontend
                   Swal.fire("Deleted!", "The Delivery Receipt has been deleted.", "success");   
            }).catch(error =>{
                console.error("Error deleting Delivery Receipt:", error);
                Swal.fire("Error", "Something went wrong", "error");
            });
          }
        })
    }
  }
};
</script>