<template>

<div class="mt-10 font-roboto">
    <div  class="flex space-x-4 justify-end">
        <archivefilter @search ="applySearch" @updateFilters="updateArchivedFilter"/>
    </div>
<table class="table-auto w-full border-collapse mt-4 shadow-lg">
        <thead class="h-14 bg-custom-blue text-white  dark:bg-custom-table dark:border-b ">
            <tr class="bg-custom-blue text-white ">
              <th class="px-6 py-4 font-bold text-center dark:bg-custom-table ">Date Added</th>
              <th class="px-6 py-4 font-bold text-center dark:bg-custom-table">DR#</th>
              <th class="px-6 py-4 font-bold text-center dark:bg-custom-table">Name</th>
              <th class="px-6 py-4 font-bold text-center dark:bg-custom-table">Project Name</th>
              <th class="px-6 py-4 font-bold text-center dark:bg-custom-table">Approved by</th>
              <th class="px-6 py-4 font-bold text-center dark:bg-custom-table">Status</th>
              <th class="px-4 py-2 border-0 font-bold text-center dark:bg-custom-table">Action</th>
            </tr>
          </thead>
          <tbody>
            
                    <!-- ✅ Fixed the v-if condition and colspan -->
                    <tr v-if="drs.length === 0">
                        <td colspan="7" class="text-center py-4">No materials found</td>
                    </tr>

                    <tr v-for="(dr, index) in drs" :key="index" class="bg-gray-200 border-b hover:bg-white">
                        <td class="px-6 py-4 text-center">{{ formatDate(dr.created_at) }}</td>

                        <td @click="viewDrArchived(dr.id)" class="px-6 py-4 text-center">
                        <button class="hover:underline custom-bg-blue">
                            {{ dr.dr_number }}
                        </button>
                        </td>

                        <td class="px-6 py-4 text-center">{{ dr.name }}</td>
                        <td class="px-6 py-4 text-center">{{ dr.project_name }}</td>

                        <!-- ✅ Fixed reference to approved_by_name -->
                        <td class="px-6 py-4 text-center">
                        {{ dr.approved_by_name }}
                        </td>

                        <td class="px-6 py-4 text-center">
                        <span 
                            class="p-2 rounded-xl text-white font-semibold"
                            :class="{
                         'bg-green-300 text-green-600  dark:bg-green-700 dark:text-green-300': dr.status === 'approved',
                      'bg-red-300 text-red-600 dark:bg-red-700 dark:text-red-300 ': dr.status  === 'rejected',
                      'bg-yellow-300 text-yellow-600 dark:bg-yellow-700 dark:text-yellow-300': dr.status  === 'pending'
                            }"
                        >
                            {{ dr.status }}
                        </span>
                        </td>

                        <td class="text-center px-4 py-2 border-0 space-x-4 item-center justify-center ">
                            <button  @click="restoreDr(dr.id, dr.dr_number)"  class="text-gray-500 dark:text-custom-white hover:underline">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                                </svg>
                            </button>

                            <button  @click="deleteDr(dr.id, dr.dr_number)"  class="text-gray-500 hover:underline dark:text-custom-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    </tbody>
        </table>
          <!-- Pagination -->
          <div class="flex items-center justify-center py-2 px-4 bg-white shadow-md z-50 dark:bg-custom-table">
                                <!-- Previous Button -->
                                <button 
                                    @click="fetchArchivedDr(currentPage - 1)" 
                                    :disabled="currentPage === 1" 
                                    class="text-lg px-4 py-2 rounded-lg disabled:opacity-50 hover:bg-gray-100 dark:hover:bg-custom-hover"
                                >
                                    ←
                                </button>

                                <!-- Pagination Numbers -->
                                <span 
                                    v-for="page in lastPage" 
                                    :key="page"
                                    @click="fetchArchivedDr(page)"
                                    class="mx-2 px-3 py-2 cursor-pointer rounded-lg  dark:bg-custom-table"
                                    :class="{'bg-blue-500 text-white': currentPage === page, 'hover:bg-gray-200': currentPage !== page}"
                                >
                                    {{ page }}
                                </span>

                                <!-- Next Button -->
                                <button 
                                    @click="fetchArchivedDr(currentPage + 1)" 
                                    :disabled="currentPage === lastPage" 
                                    class="text-lg px-4 py-2 rounded-lg disabled:opacity-50 hover:bg-gray-100 dark:hover:bg-custom-hover"
                                >
                                    →
                                </button>
                            </div>

       <!-- View Delivery Receipt Modal -->
       <viewDr 
          v-if="showViewDrModal" 
          :show="showViewDrModal" 
          :item="selectedDr" 
          :materials="selectedmaterials"  
          @close="showViewDrModal = false" 
        />
 

    </div>  
</template>
<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import { mapGetters } from 'vuex';
import archivefilter from '@/components/admin/Transaction/archivefilter.vue';
import viewDr from '@/components/admin/Transaction/viewDr.vue';
export default {
    components:{
        archivefilter,
        viewDr,
    },
data() {
    return {
        drs:[],
        selectedDr: {},
        selectedmaterials: [],
        tempSearchQuery:"",
        selectedStatus:'',
        selectedDateAdded:'',
        currentPaage: 1,
        lastPage: 1,
        showViewDrModal: false,
        
    }
},
mounted(){
        this.fetchArchivedDr();
},
computed: {
      ...mapGetters('auth', ['user', 'userRole']),
      filteredArchive() {
        return this.drs.filter(dr => {
          const matchesStatus = this.selectedStatus ? dr.status === this.selectedStatus : true;
          const matchesDateAdded = this.selectedDateAdded ? new Date(dr.created_at).toISOString().split("T")[0] === this.selectedDateAdded : true;
          return matchesStatus && matchesDateAdded;
        });
      },
    },
  
methods:{
    async fetchArchivedDr(page = 1) {
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

    try {
        const response = await axios.get('/api/drs/archived', { params });
        console.log('Successfully Fetched Archived Delivery Receipts:', response.data);

        this.drs = response.data.data.map(dr => ({
            ...dr,
            approved_by_name: dr.approver
                ? `${dr.approver.first_name} ${dr.approver.middle_name ? dr.approver.middle_name + ' ' : ''}${dr.approver.last_name}`
                : 'Pending'
        }));

        // ✅ Fix typo here
        this.currentPage = response.data.current_page;
        this.lastPage = response.data.last_page;

    } catch (error) {
        console.error('Failed to Fetch Archived Delivery Receipt', error);
    }
},

    applySearch(query){
        this.tempSearchQuery = query;
        this.fetchArchivedDr(1);

    },
    updateArchivedFilter(filter){
        this.selectedDateAdded = filter.created_at || '';
        this.selectedStatus = filter.status || '';
        this.currentPage = 1;
        this.fetchArchivedDr(1);
    },

    async restoreDr(id, dr_number) {
        try {
            const result = await Swal.fire({
            title: `Are you sure you want to restore #${dr_number}?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, restore it!",
            cancelButtonText: "Cancel",
            });
            if (result.isConfirmed) {
            await axios.patch(`/api/drs/${id}/restore`);
            Swal.fire("Restored", "The Delivery receipt has been restored", "success");

            // ✅ Refresh the archived list
            this.fetchArchivedDr();
            }
        } catch (error) {
            console.error('Failed to Restore Delivery Receipt', error);
            Swal.fire("Error", "Failed to restore Delivery Receipt", "error");
        }
        },

     async deleteDr(id, dr_number){
        try{
            const result = await Swal.fire({
            title: `Are you sure you want to delete permanently #${dr_number}?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
            });


            if (result.isConfirmed) {
                await axios.delete(`/api/drs/${id}/force-delete`);
                Swal.fire("Deleted", "The Delivery Receipt has been deleted permanently", "success")
            }
            this.fetchArchivedDr();

        }catch(error){
            console.error('Failed to Restore Delivery Receipt', error);
            Swal.fire("Error", "Failed to delete delivery receipt", "error")
        }
     }, 

     async viewDrArchived(id){
          try{
           const response = await axios.get(`/api/Dr/${id}`);
            console.log('View Develiry Receipt', response.data);
                if (response.data) {
                       // ✅ Use separate state for modal data
                        this.selectedDr = response.data.item || {}; 
                        this.selectedmaterials = response.data.materials || [];
                        this.showViewDrModal = true; 
                }
          } catch (error){
                console.error('Failed to fetch to view dr', error)
          }

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


}

};


</script>