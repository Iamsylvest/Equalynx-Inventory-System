<template>
   
    <div class="p-8 ">
     <header class="flex flex-row justify-between items-center">
         <h1 class="text-2xl  whitespace-nowrap">Activity Logs</h1>
         <div class="flex items-end justify-end">
           <ActivityLogsNotification />
           <ActivityLogsProfile />
         </div>
     </header>
     <div  >
        <div class="md:flex md:items-center md:justify-end space-x-6 mt-4">
           <ActivityLogsFillter @search="updateSearch" @filter="applyFilter"/>
        </div>
    </div>

 <!-- Activity Logs Table -->
    <table class="table-auto w-full border-collapse mt-5 shadow-lg">
      <thead class="h-14">
        <tr class="bg-custom-blue text-white">
          <th class="px-4 py-2 border-0 text-center font-bold">User</th>
          <th class="px-4 py-2 border-0 text-center font-bold">Date Added</th>
          <th class="px-4 py-2 border-0 text-center font-bold">Time</th>
          <th class="px-4 py-2 border-0 text-center font-bold">Description</th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="filteredLogs.length === 0" >
              <td colspan="6" class="text-center py-4">No Activity Logs found</td>
            </tr>
        <tr v-for="(log, index) in filteredLogs" :key="index" class="text-center border border-l-0 border-r-0  ">
            <td class="px-4 py-2 flex flex-col justify-center ">
              <span class="font-semibold"> {{ log.performed_by }} </span>
              <span 
              class="font-semibold"
              :class="{
                ' text-green-600': log.role === 'admin',
                ' text-orange-600': log.role === 'manager',
                ' text-red-600': log.role === 'procurement',
                ' text-yellow-600': log.role === 'warehouse_staff'
                  
              }"
              >{{ log.role }}</span>
            </td>
          <td class="px-4 py-2 ">{{ formatDate(log.timestamp) }}</td>
          <td class="px-4 py-2 ">{{ formatTime(log.timestamp) }}</td>
          <td class="px-4 py-2 ">{{ log.action }}</td>
        </tr>
      </tbody>
    </table>
    <div class="flex items-center justify-center py-2 px-4 bg-white shadow-md z-50">
        <!-- Previous Button -->
        <button 
          @click="fetchLogs(currentPage - 1)" 
          :disabled="currentPage === 1" 
          class="text-lg px-4 py-2 rounded-lg disabled:opacity-50 hover:bg-gray-100"
        >
          ←
        </button>

        <!-- Pagination Numbers -->
        <span 
          v-for="page in lastPage" 
          :key="page"
          @click="fetchLogs(page)"
          class="mx-2 px-3 py-2 cursor-pointer rounded-lg"
          :class="{'bg-blue-500 text-white': currentPage === page, 'hover:bg-gray-200': currentPage !== page}"
        >
          {{ page }}
        </span>

        <!-- Next Button -->
        <button 
          @click="fetchLogs(currentPage + 1)" 
          :disabled="currentPage === lastPage" 
          class="text-lg px-4 py-2 rounded-lg disabled:opacity-50 hover:bg-gray-100"
        >
          →
        </button>
      </div>
  </div>

 
 
 </template>
 

<script>
import axios from 'axios';
import { mapGetters } from 'vuex'; // ✅ Import mapGetters
import ActivityLogsFillter from '@/components/admin/ActivityLogs/ActivityLogsFillter.vue';
import ActivityLogsNotification from '@/components/admin/ActivityLogs/ActivityLogsNotification.vue';
import ActivityLogsProfile from '@/components/admin/ActivityLogs/ActivityLogsProfile.vue';


export default {
    components :{
        ActivityLogsFillter,
        ActivityLogsNotification,
        ActivityLogsProfile,

 
    },
    data() {
      return {
          logs: [], // Store logs here
          tempSearchQuery:"",
          selectedRole: '',
          selectedDate: '',
          currentPage: 1,
          lastPage: 1,
      };
    },
    computed: {
      ...mapGetters(['isAuthenticated']), // Map the Vuex getter
      filteredLogs(){
          return this.logs.filter(log => {
              const matchesRole = this.selectedRole ? log.role === this.selectedRole : true;
              const matchesDateAdded = this.selectedDate && log.created_at 
                ? (new Date(log.created_at).toISOString().split("T")[0] === this.selectedDate)
                : true;
              return matchesRole && matchesDateAdded;
          });
      }, 
    },
   mounted(){
      this.fetchLogs();
   },
    methods:{
      applyFilter(filters){
        this.selectedDate = filters.created_at || '';
        this.selectedRole = filters.role || '';
        this.currentPage_Return = 1;  // Reset to the first page when applying filterssylv
        this.fetchLogs(1);// Fetch data with the new filters
      },
      updateSearch(query){
          this.tempSearchQuery = query;
          this.fetchLogs(1);// ✅ Fetch data when searching
      },
      
      async fetchLogs(page = 1) {

        const params = {page};
        if (this.tempSearchQuery) { params.search = this.tempSearchQuery};
        if (this.selectedDate)  {let formattedDate = new Date(this.selectedDate).toISOString().split("T")[0]; params.created_at = formattedDate};
        if (this.selectedRole) {params.role = this.selectedRole};

        try {
          const response = await axios.get('/api/activity-logs', {params});
                console.log("API Response:", response.data); // ✅ Debugging output
                this.logs = Array.isArray(response.data) 
                  ? response.data.filter(log => log !== null) // Remove null entries
                  : [];

                  this.logs = response.data.data;
                  this.currentPage = response.data.current_page;
                  this.lastPage = response.data.last_page

              } catch (error) {
                console.error('Error fetching logs', error);
              }
            },
     formatDate(timestamp){
              return new Date(timestamp).toLocaleDateString('en-US', {
                  year: 'numeric',
                  month: 'short',
                  day: 'numeric'
              });
            },
            formatTime(timestamp) {
                if (!timestamp) return "Invalid Time";

                const date = new Date(timestamp);
                date.setHours(date.getHours() + 8); // Convert UTC to PH time

                return date.toLocaleTimeString("en-US", { 
                  hour: "2-digit", 
                  minute: "2-digit",
                  hour12: true 
                });
              }
    },

};
    
</script>