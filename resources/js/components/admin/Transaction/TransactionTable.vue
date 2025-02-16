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
                <button class="text-gray-500 hover:text-custom-blue hover:underline w-full sm:w-auto">
                  <!-- Icon here -->
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
  }
};
</script>