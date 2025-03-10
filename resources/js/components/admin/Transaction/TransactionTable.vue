<template>
    <div class="mt-10 font-roboto">
      <!-- Main container -->
      <div class="flex flex-wrap items-center justify-between relative top-[-10px] w-full">
        <!-- Left Section: Buttons -->
        <div class="flex space-x-4">
          <button
            class="p-3 px-4 text-sm font-bold border-b-2 shadow-md rounded-md focus:outline-none w-full sm:w-auto"
            @click="showTable = 'delivery'"
            :class="{
              'bg-custom-blue text-white': showTable === 'delivery',
              'hover:bg-custom-blue hover:text-white': showTable !== 'delivery'
            }"
          >
            Delivery Receipt
          </button>
  
          <button
            class="p-3 px-4 text-sm font-bold border-b-2 shadow-md rounded-md focus:outline-none w-full sm:w-auto"
            @click="showTable = 'return'"
            :class="{ 
              'bg-custom-blue text-white': showTable === 'return',
              'hover:bg-custom-blue hover:text-white': showTable !== 'return'
            }"
          >
            Return Receipt
          </button>
        </div>
        <div class="flex space-x-4">
          <TransactionFillter @search="applySearch" @updateFilters="updateFilters" v-if="showTable === 'delivery'"/>
          <returnFillter @searchReturn="applySearchReturn" @updateFiltersReturn="updateFiltersReturn" v-if="showTable === 'return'"/>
            <addDR @addedDR="handleDRadded"  @closeModal="showDrmodal = false" v-if="userRole === 'procurement'" />
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
            <tr v-if="filteredDR.length === 0" >
              <td colspan="6" class="text-center py-4">No materials found</td>
            </tr>
            <tr v-for="(item, index) in filteredDR" :key="index" class="bg-white border-b">
              <td class="px-6 py-4 text-center">{{ formatDate(item.created_at) }}</td>

              <td @click="viewDr(item.id)" class="px-6 py-4 text-center">
                <button class="hover:underline custom-bg-blue ">
                  {{ item.dr_number}}
                </button>
              </td>
              <td class="px-6 py-4 text-center">{{ item.name }}</td>
              <td class="px-6 py-4 text-center">{{ item.project_name }}</td>

              <td class="px-6 py-4 text-center" v-if="item.approver !== 'manager'">  
                {{ item.approver ? (item.approver.first_name + ' ' + (item.approver.middle_name ? item.approver.middle_name + ' ' : '') + item.approver.last_name) : 'pending' }}
              </td>

              <td class="px-6 py-4 text-center">
                <span  class="p-2 rounded-xl text-white font-semibold"
                :class="{
                'bg-green-300 text-green-600': item.status === 'approved',
                'bg-red-300 text-red-600': item.status  === 'rejected',
                'bg-yellow-300 text-yellow-600': item.status  === 'pending'
              }">
              {{ item.status }}
                </span>
           
              </td>
              
              <td class="text-center px-4 py-2 border-0 space-x-4 flex item-center justify-center mt-2">
                <button @click="addRR(item.id)" class="text-gray-500 hover:underline w-full sm:w-auto" >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9.75h4.875a2.625 2.625 0 0 1 0 5.25H12M8.25 9.75 10.5 7.5M8.25 9.75 10.5 12m9-7.243V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185Z" />
                  </svg>

                </button>

                <button @click="editDR(item.id)" class="text-gray-500 hover:underline w-full sm:w-auto" v-if="item.status !== 'approved' && item.status !== 'rejected'" >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487 18.5 2.75a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                  </svg>
                </button>

                <button  @click="downloadPDF(item.id)" 
                        class="text-gray-500 hover:underline w-full sm:w-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                </button>
                            
                <button @click="deleteDr(item.id, item.dr_number)"  class="text-gray-500 hover:underline">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
              </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    

          <!-- RETURN TABLE -->
      
      <div>
        <table v-if="showTable === 'return'" class="table-auto w-full border-collapse mt-1 shadow-lg">
          <thead class="h-14 bg-gray-100">
            <tr class="bg-custom-blue text-white">
              <th class="px-6 py-4 font-bold text-center">Date Added</th>
              <th class="px-6 py-4 font-bold text-center">RR# </th>
              <th class="px-6 py-4 font-bold text-center">DR#</th>
              <th class="px-6 py-4 font-bold text-center">Name</th>
              <th class="px-6 py-4 font-bold text-center">Project Name</th>
              <th class="px-6 py-4 font-bold text-center">Approved by:</th>
              <th class="px-6 py-4 font-bold text-center">Status</th>
              <th class="px-4 py-2 border-0 font-bold text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="filteredRR.length === 0" >
              <td colspan="6" class="text-center py-4">No materials found</td>
            </tr>
              <tr v-for="(rr, index) in filteredRR" :key="index">
                <td class="px-6 py-4 text-center">{{ formatDate(rr.created_at) }}</td>

                <td @click="viewRR(rr.id)"  class="px-6 py-4 text-center">
                  <button class="hover:underline custom-bg-blue">
                    {{ rr.rr_number }}
                  </button>
                </td>

                <td class="px-6 py-4 text-center">
                  <button class="hover:underline custom-bg-blue">
                    {{ rr.dr ? rr.dr.dr_number : 'N/A' }}
                    
                  </button>
                </td>

                <td class="px-6 py-4 text-center">{{ rr.name }}</td>
                <td class="px-6 py-4 text-center">{{ rr.project_name }}</td>

                <td class="px-6 py-4 text-center">
                  {{ rr.approver 
                        ? rr.approver.first_name + ' ' + 
                          (rr.approver.middle_name ? rr.approver.middle_name + ' ' : '') + 
                          rr.approver.last_name 
                        : 'N/A' 
                    }}
            </td>

                <td class="px-6 py-4 text-center">
                  <span class="p-2 rounded-xl text-white font-semibold"
                    :class="{
                      'bg-green-300 text-green-600': rr.status === 'approved',
                      'bg-red-300 text-red-600': rr.status === 'rejected',
                      'bg-yellow-300 text-yellow-600': rr.status === 'pending'
                    }">
                    {{ rr.status }}
                  </span>
                </td>
                <td class="text-center px-4 py-2 border-0 space-x-4 flex item-center justify-center mt-2">
      

                <button @click="editRR(rr.id)"  class="text-gray-500 hover:underline w-full sm:w-auto" v-if="rr.status !== 'approved' && rr.status !== 'rejected'">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487 18.5 2.75a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                  </svg>
                </button>

                <button  @click="downloadPDFrr(rr.id)" 
                        class="text-gray-500 hover:underline w-full sm:w-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                </button>
                            
                <button @click="deleteRr(rr.id, rr.rr_number)"  class="text-gray-500 hover:underline">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
              </button>
              </td>
              </tr>
            </tbody>

            <!-- Show message when there is no data -->

        </table>

           <!-- Pagination -->
          <div v-if="showTable === 'delivery'"  class="flex items-center justify-center py-2 px-4 bg-white shadow-md z-50">
                    <!-- Previous Button -->
                    <button 
                      @click="fetchDRs(currentPage - 1)" 
                      :disabled="currentPage === 1" 
                      class="text-lg px-4 py-2 rounded-lg disabled:opacity-2 hover:bg-gray-100"
                    >
                      ←
                    </button>

                    <!-- Pagination Numbers -->
                    <span 
                      v-for="page in lastPage" 
                      :key="page"
                      @click="fetchDRs(page)"
                      class="mx-2 px-3 py-2 cursor-pointer rounded-lg"
                      :class="{'bg-blue-500 text-white': currentPage === page, 'hover:bg-gray-200': currentPage !== page}"
                    >
                      {{ page }}
                    </span>

                    <!-- Next Button -->
                    <button 
                      @click="fetchDRs(currentPage + 1)" 
                      :disabled="currentPage === lastPage" 
                      class="text-lg px-4 py-2 rounded-lg disabled:opacity-2 hover:bg-gray-100"
                    >
                      →
                    </button>
              </div>

                        <!-- Pagination -->
          <div v-if="showTable === 'return'"  class="flex items-center justify-center py-2 px-4 bg-white shadow-md z-50">
                    <!-- Previous Button -->
                    <button 
                      @click="fetchRRs(currentPage_Return - 1)" 
                      :disabled="currentPage_Return === 1" 
                      class="text-lg px-4 py-2 rounded-lg disabled:opacity-2 hover:bg-gray-100"
                    >
                      ←
                    </button>

                    <!-- Pagination Numbers -->
                    <span 
                      v-for="page in lastPage_Return" 
                      :key="page"
                      @click="fetchRRs(page)"
                      class="mx-2 px-3 py-2 cursor-pointer rounded-lg"
                      :class="{'bg-blue-500 text-white': lastPage_Return === page, 'hover:bg-gray-200': lastPage_Return !== page}"
                    >
                      {{ page }}
                    </span>

                    <!-- Next Button -->
                    <button 
                      @click="fetchRRs(currentPage + 1)" 
                      :disabled="currentPage_Return === lastPage_Return" 
                      class="text-lg px-4 py-2 rounded-lg disabled:opacity-2 hover:bg-gray-100"
                    >
                      →
                    </button>
              </div>
              
         
            </div>
          <!-- View Delivery Receipt Modal -->
      <viewDr 
          v-if="showViewDrModal" 
          :show="showViewDrModal" 
          :item="selectedDR" 
          :materials="selectedmaterials"  
          @close="showViewDrModal = false" 
        />
 
                  <!-- View Delivery Receipt Modal -->
      <viewRR
          v-if="viewReturn" 
          :viewReturn="viewReturn" 
          :item="selectedRR" 
          :materials="selectedReturnMaterials"  
          @closeViewReturnModal="viewReturn = false" 
        />
            


           <!-- Edit Delivery Receipt Modal -->
           <editDR
            v-if="showEditDrModal"
            :showEditDR="showEditDrModal"
            :item="selectedDR"
            :materials="selectedmaterials" 
            @closeModal="closeEditDrModal" 
          />

           <!-- Create Return Receipt Modal -->
          <addRR
            v-if="showEditDR_Return"
            :showEditDR_Return="showEditDR_Return"
            @closeModalReturnModal="closeCreateRRmodal"
            :item="selectedDR"
            :materials="selectedmaterials" 
          />
               <!-- Create Return Receipt Modal -->
          <editRR
            v-if="showEditReturn"
            :showEditReturn="showEditReturn"
            @closeEditReturn="closeEditRRmodal"
            :item="selectedRR"
            :materials="selectedReturnMaterials" 
          />
          
  
             
    </div>
</template>
  
<script>
import TransactionNotification from '@/components/admin/Transaction/TransactionNotification.vue';
import TransactionProfile from '@/components/admin/Transaction/TransactionProfile.vue';
import addDR from '@/components/admin/Transaction/addDR.vue';
import viewDr from '@/components/admin/Transaction/viewDr.vue';
import editDR from '@/components/admin/Transaction/editDR.vue';
import editRR from '@/components/admin/Transaction/editRR.vue';
import viewRR from '@/components/admin/Transaction/viewRR.vue';
import addRR from '@/components/admin/Transaction/addRR.vue';
import TransactionFillter from '@/components/admin/Transaction/TransactionFillter.vue';
import returnFillter from '@/components/admin/Transaction/returnFillter.vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import {mapGetters} from 'vuex';

export default {
    components: {
      TransactionNotification,
        TransactionProfile,
        addDR,
        viewDr,
        editDR,
        editRR,
        TransactionFillter,
        addRR,
        viewRR,
        returnFillter,
     
     
    },
    data() {
        return {
            showTable: 'delivery', // Default value
            showViewDrModal: false, // Control modal visibility
            showEditReturn: false,
            viewReturn: false,
            selectedDR: null, // ✅ Initialize as null
            selectedmaterials: [], // ✅ Initialize as empty array
            selectedRR: null, // ✅ Initialize as null
            selectedReturnMaterials: [], // ✅ Initialize as empty array
            drs: [], // Fixed naming for clarity
            returnRR:[],
            showEditDrModal:false,
            showEditDR_Return:false,
            tempSearchQuery:"",
            selectedStatus:'',
            selectedDateAdded:'',
            tempSearchQueryReturn:"",
            selectedStatusReturn:'',
            selectedDateAddedReturn:'',
            currentPage_Return: 1,
            lastPage_Return: 1,
            currentPage: 1,
            lastPage: 1,

    
          
           

        };
    },
    
    mounted() {
        this.fetchDRs(); // Fetch data when the component loads
        this.fetchRRs();
    },
    computed: {
      ...mapGetters('auth', ['user', 'userRole']),
      filteredDR() {
        return this.drs.filter(dr => {
          const matchesStatus = this.selectedStatus ? dr.status === this.selectedStatus : true;
          const matchesDateAdded = this.selectedDateAdded ? new Date(dr.created_at).toISOString().split("T")[0] === this.selectedDateAdded : true;
          return matchesStatus && matchesDateAdded;
        });
      },
      filteredRR(){
          return this.returnRR.filter(rr => {
              const matchesStatus = this.selectedStatusReturn ? rr.status === this.selectedStatusReturn : true;
              const matchesDateAdded = this.selectedDateAddedReturn ?  new Date(rr.created_at).toISOString().split("T")[0] === this.selectedDateAddedReturn : true;
              return matchesStatus && matchesDateAdded;
          });
    }, 
    },

    methods: {
  
      async downloadPDF(id) {
        const apiUrl = `/api/pdf/generate/${id}`; // Relative URL without the full domain
        console.log('Requesting PDF with ID:', id);
        
        try {
          const response = await axios.get(apiUrl, {
            responseType: 'blob', // Important for binary data
            headers: {
              "Accept": "application/pdf"
            }
          });

          const blob = response.data;
          const blobUrl = window.URL.createObjectURL(blob);

          const a = document.createElement("a");
          a.href = blobUrl;
          a.download = `DeliveryReceipt_${id}.pdf`;
          document.body.appendChild(a);
          a.click();
          a.remove();
        } catch (error) {
          console.error("Download error:", error);
        }
      },
      async downloadPDFrr(id) {
        const apiUrl = `/api/pdf/generate/${id}`; // Relative URL without the full domain
        console.log('Requesting PDF with ID:', id);
        
        try {
          const response = await axios.get(apiUrl, {
            responseType: 'blob', // Important for binary data
            headers: {
              "Accept": "application/pdf"
            }
          });

          const blob = response.data;
          const blobUrl = window.URL.createObjectURL(blob);

          const a = document.createElement("a");
          a.href = blobUrl;
          a.download = `ReturnReceipt_${id}.pdf`;
          document.body.appendChild(a);
          a.click();
          a.remove();
        } catch (error) {
          console.error("Download error:", error);
        }
      },

  
   handleDRadded(newDr) {
            this.drs.push(newDr);
            this.fetchDRs(); // Refetch all DRs to ensure consistency
        },

        formatDate(date) {
        if (!date || isNaN(new Date(date).getTime())) {
          console.warn("❌ Invalid date detected:", date);
          return null; // Or return a default value
        }
        return new Date(date).toLocaleDateString("en-US", {
          year: "numeric",
          month: "short",
          day: "numeric"
        });
      },

      
    deleteDr(deliveryReceiptId, dr_number){
        Swal.fire({
          title: `Are you sure you want to delete #${dr_number}?`,
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
    },
    async viewDr(id) {
      try {
        const response = await axios.get(`/api/Dr/${id}`);
        if (response.data) {
          this.selectedDR = response.data.item || {};
          this.selectedmaterials = response.data.materials || [];
          this.showViewDrModal = true; // Show View Modal
        }
      } catch (error) {
        console.error("Error fetching delivery receipt:", error);
      }
    },
    async viewRR(id) {
      try {
        const response = await axios.get(`/api/Rr/${id}`);
        console.log('API Response:', response.data);  // Log the response to see what it returns
        if (response.data) {
          this.selectedRR = response.data.item || {}; // Default to empty object if no item
          this.selectedReturnMaterials = response.data.materials || [];
          this.viewReturn = true; // Show View Modal only after data is ready
        }
      } catch (error) {
        console.error("Error fetching delivery receipt:", error);
      }
    },

    openEditDrModal() {
    this.showEditDrModal = true; // Show Edit Modal
    },

    closeEditDrModal() {
      this.showEditDrModal = false; // Close Edit Modal
    },
  

    updateSelectedMaterials(updatedMaterials) {
        this.selectedmaterials = [...updatedMaterials]; // Ensure reactivity
    },


    async editDR(id) {
      try {
        const response = await axios.get(`/api/Dr/${id}`);
        console.log("API Response:", response.data);
        console.log("Materials in response:", response.data.item?.materials);

        if (response.data) {
          // Temporarily clear the selectedDR before assigning new data
          this.selectedDR = null;

          this.$nextTick(() => {
            this.selectedDR = { ...response.data.item }; // ✅ Ensure reactivity
            this.selectedmaterials = [...(response.data.item?.materials || [])];

            console.log("Selected Materials:", this.selectedmaterials);
            this.showEditDrModal = true;
          });
        }
      } catch (error) {
        console.error("Error fetching Delivery Receipt for edit:", error);
      }
    },
    async editRR(id){
        try{
          const response = await axios.get(`/api/Rr/${id}`);
          console.log("Api response", response.data);
          console.log("Materials in response", response.data.item?.materials);

          if (response.data) {
              this.selectedRR = null;
                this.$nextTick(() => {
                  this.selectedRR = {...response.data.item};
                  this.selectedReturnMaterials = [...(response.data.item?.materials || [])]
                  console.log("Selected Materials:", this.selectedReturnMaterials);
                  this.showEditReturn = true;
                });
          }
        } catch(error){
            console.error("Error Fetching Return Receipt for edit:", error);
        }
    },

    closeEditRRmodal(){
      this.showEditReturn = false;
    },

    handleUpdatedDR(updatedDR) {
        const index = this.drs.findIndex(dr => dr.id === updatedDR.id);
        if (index !== -1) {
            this.drs[index] = { ...updatedDR }; 
            this.drs = [...this.drs]; // ✅ Force Vue 3 reactivity
        } else {
            this.drs.push(updatedDR); // In case it's a new entry
        }

        this.showEditDrModal = false; // Close modal
    },

    handleUpdaterRR(updatedRR){
        const index = this.returnRR.findIndex(rr => rr.id === updatedRR.id);
        if (index !== -1) {
           this.returnRR[index] = {...updatedRR};
           this.returnRR = [...this.returnRR]; // ✅ Force reactivity
        } else{
          this.returnRR.push[updatedRR];// If it's a new entry
        }
    },

    async fetchDRs(page = 1) {
        const params = { page };
        
        if (this.selectedStatus) {
          params.status = this.selectedStatus;
        }
        if (this.selectedDateAdded) {
          let formattedDate = new Date(this.selectedDateAdded).toISOString().split("T")[0];
          params.created_at = formattedDate;
        }
          if (this.tempSearchQuery) {
            params.search = this.tempSearchQuery;
          }

        console.log('📡 Fetching from API with:', params);

        try {
          const response = await axios.get('/api/Dr', { params });
          console.log('✅ API Response:', response.data);

          this.drs = response.data.data.map(dr=> ({
            ...dr,
            approved_by_name: dr.approver
            ? `${dr.approver.first_name} ${dr.approver.middle_name ? dr.approver.middle_name + ' ' : ''}${dr.approver.last_name}`
          : 'Pending'
          }));
          this.currentPage = response.data.current_page;
          this.lastPage = response.data.last_page;
        } catch (error) {
          console.error('❌ Error fetching DRs:', error);
        }
      },

      updateFilters(filters) {
      this.selectedStatus = filters.status || '';
      this.selectedDateAdded = filters.created_at || '';
      this.currentPage = 1;  // Reset to the first page when applying filters
      this.fetchDRs(1); // Fetch data with the new filters
    },
      applySearch(query) {
        this.tempSearchQuery = query;
        this.fetchDRs(1); // ✅ Fetch data when searching
    },


      async fetchRRs(page = 1){
        const params = {page};
        if (this.selectedStatusReturn) {params.status = this.selectedStatusReturn};
        if(this.selectedDateAddedReturn) {let formattedDate = new Date(this.selectedDateAddedReturn).toISOString().split("T")[0]; params.created_at = formattedDate};
        if(this.tempSearchQueryReturn) {params.search = this.tempSearchQueryReturn};
        console.log('Fetching Return Receipt:', params);
        console.log('selectedStatusReturn:', this.selectedStatusReturn);
        try{
          const response = await axios.get('api/Rr', {params});
          console.log('Apis response', response.data);

          this.returnRR = response.data.data.map(rr=> ({
            ...rr,
            approved_by_name: rr.approver
            ? `${rr.approver.first_name} ${rr.approver.middle_name ? rr.approver.middle_name + ' ' : ''}${rr.approver.last_name}`
          : 'N/A'
          }));
          this.currentPage_Return = response.data.current_page;
          this.lastPage_Return = response.data.last_page;
        }catch(error){
          console.error('Failed to Fetch RRs', error)
        }
      },
    
      updateFiltersReturn(filters){
        this.selectedDateAddedReturn = filters.created_at || '';
        this.selectedStatusReturn = filters.status || '';
        this.currentPage_Return = 1;  // Reset to the first page when applying filters
        this.fetchRRs(1);// Fetch data with the new filters
      },
      applySearchReturn(query){
          this.tempSearchQueryReturn = query;
          this.fetchRRs(1);// ✅ Fetch data when searching
      },
      


    // RETURN TABLE LOGIC IN FRONT END

    openCreateRRmodal(){
      this.showEditDR_Return = true;
    },
    closeCreateRRmodal(){
    this.showEditDR_Return = false;
    
    },

    async addRR(id) {
      try {
        const response = await axios.get(`/api/Dr/${id}`);
        console.log("API Response:", response.data);
        console.log("Materials in response:", response.data.item?.materials);

        if (response.data) {
          // Temporarily clear the selectedDR before assigning new data
          this.selectedDR = null;

          this.$nextTick(() => {
            this.selectedDR = { ...response.data.item }; // ✅ Ensure reactivity
            this.selectedmaterials = [...(response.data.item?.materials || [])];

            console.log("Selected Materials:", this.selectedmaterials);
            this.showEditDR_Return = true;
          });
        }
      } catch (error) {
        console.error("Error fetching Delivery Receipt for edit:", error);
      }
    },


    deleteRr(returnReceiptId, rr_number){
        Swal.fire({
          title: `Are you sure you want to delete #${rr_number}?`, // ✅ Proper string interpolation
          text: "You won't be able to revert this!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#d33",
          cancelButtonColor: "#3085d6",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
          if (result.isConfirmed) {
            axios.delete(`/api/Rr/${returnReceiptId}`).then(() => {
                // ✅ Fetch updated list after deletion
                this.fetchRRs();
                   // Update the frontend
                   Swal.fire("Deleted!", "The Return Receipt has been deleted.", "success");   
            }).catch(error =>{
                console.error("Error deleting Return Receipt:", error);
                Swal.fire("Error", "Something went wrong", "error");
            });
          }
        })
    },


  },

};
</script>