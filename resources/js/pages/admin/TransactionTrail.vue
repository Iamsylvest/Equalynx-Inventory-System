<template>
    <div  class="p-8 dark:text-custom-white ">
     <header class="flex flex-row justify-between items-center">
         <h1 class="text-2xl  whitespace-nowrap">Transaction Trail</h1>
         <div class="flex items-end justify-end">
           <Notification />
           <Profile />
         </div>
     </header>
     <div class="md:flex md:items-center md:justify-end space-x-6 mt-4">
      <div class="flex items-center space-x-4">
        <input 
          type="text" 
          v-model="searchUsername" 
          @input="filterLogs" 
          placeholder="Search by user" 
          class="mr-5 w-[250px] md:w-[148px] mb-5 sm:mb-5 md:mb-0 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-custom-table dark:focus:ring-white"
        />
        <button 
          @click="toggleSort" 
          class="mr-5 w-[250px] md:w-[148px] mb-5 sm:mb-5 md:mb-0 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-custom-table dark:focus:ring-white">
          Sort by Date {{ isSortedAsc ? '▲' : '▼' }}
        </button>
      </div>
    </div>

    <!-- Table and modal code here -->
    <div class="max-h-[600px] overflow-y-auto mt-6 rounded-lg shadow-lg">
      <table class="table-auto w-full border-collapse bg-white dark:bg-gray-800">
        <thead class="bg-custom-blue text-white h-14 sticky top-0 z-10">
          <tr>
            <th class="px-4 py-2 text-center font-bold dark:bg-custom-table">User</th>
            <th class="px-4 py-2 text-center font-bold dark:bg-custom-table">Date Added</th>
            <th class="px-4 py-2 text-center font-bold dark:bg-custom-table">Time</th>
            <th class="px-4 py-2 text-center font-bold dark:bg-custom-table">Description</th>
            <th class="px-4 py-2 text-center font-bold dark:bg-custom-table">Materials</th>
            <th class="px-4 py-2 text-center font-bold dark:bg-custom-table">Action</th>
          </tr>
        </thead>
        <tbody>
          <!-- Using filteredLogs to render the rows -->
          <tr v-for="log in filteredLogs" :key="log.id" class="hover:bg-gray-100 dark:hover:bg-gray-700 text-center border border-l-0 border-r-0 h-full">
            <td class="px-4 py-3 text-center whitespace-nowrap">{{ log.performed_by }}</td>
            <td class="px-4 py-3 text-center whitespace-nowrap">{{ formatDate(log.timestamp) }}</td>
            <td class="px-4 py-3 text-center whitespace-nowrap">{{ formatTime(log.timestamp) }}</td>
            <td class="px-4 py-3 text-left">{{ log.action }}</td>
            <td class="px-4 py-3 text-center">
              <button 
                class="px-4 py-2 bg-custom-blue text-white rounded-lg hover:bg-blue-600 dark:hover:bg-blue-500 whitespace-nowrap"
                @click="openModal(log)">
                See Full Details
              </button>
            </td>
            <td class="px-4 py-3 text-center">
              <button @click="deleteLog(log.id)" class="text-gray-500 hover:underline w-full md:w-auto dark:text-custom-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

        <!-- Modal for showing materials and transaction details -->
      <div v-if="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-lg w-11/12 max-w-4xl">
          <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-300 mb-4">Transaction Details</h3>

          <!-- DR or RR Info -->
          <div class="space-y-4">
            <template v-if="selectedLog.transaction_details.delivery_receipt">
              <h4 class="font-semibold text-lg text-gray-800 dark:text-gray-300">Delivery Receipt</h4>
              <div><strong>DR Number:</strong> {{ selectedLog.transaction_details.delivery_receipt.dr_number }}</div>
              <div><strong>Status:</strong> {{ selectedLog.transaction_details.delivery_receipt.status }}</div>
              <div><strong>Remarks:</strong> {{ selectedLog.transaction_details.delivery_receipt.remarks }}</div>
              <div><strong>Location:</strong> {{ selectedLog.transaction_details.delivery_receipt.location }}</div>
            </template>

            <template v-else-if="selectedLog.transaction_details.return_receipt">
                      <h4 class="font-semibold text-lg text-gray-800 dark:text-gray-300">Return Receipt</h4>
                      <div><strong>RR Number:</strong> {{ selectedLog.transaction_details.return_receipt.rr_number }}</div>
                      <div><strong>Status:</strong> {{ selectedLog.transaction_details.return_receipt.status }}</div>
                      <div><strong>Remarks:</strong> {{ selectedLog.transaction_details.return_receipt.remarks }}</div>
                      <div><strong>Location:</strong> {{ selectedLog.transaction_details.return_receipt.location }}</div>
                    </template>
          </div>

          <!-- Materials Information -->
          <div class="mt-6 space-y-4">
            <h4 class="font-semibold text-lg text-gray-800 dark:text-gray-300">Materials</h4>
            <div v-for="material in selectedLog.transaction_details.materials" :key="material.material_name" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
              <div><strong>Material:</strong> {{ material.material_name }}</div>
              <div><strong>Measurement:</strong> {{ material.measurement }}</div>
              <div><strong>Unit:</strong> {{ material.unit }}</div>
              <div><strong>Quantity:</strong> {{ material.quantity }}</div>
            </div>
          </div>

          <!-- Close Button -->
          <div class="mt-6 text-right">
            <button 
              class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-500"
              @click="closeModal">
              Close
            </button>
          </div>
        </div>
      </div>
  </div>
</template>

<script>
import axios from 'axios';
import Notification from '@/components/admin/Notification/Notification.vue';
import Profile from '@/components/admin/Notification/Profile.vue';
import Swal from 'sweetalert2';

export default {
  components: {
    Notification,
    Profile,
  },
  data() {
    return {
      logs: [], // logs fetched from backend
      rrLogs: [],    // for RR logs
      isModalOpen: false,
      selectedLog: null,
      searchUsername: '',  // for searching by username
      isSortedAsc: true,   // for sorting by date
    };
  },
  computed: {
    filteredLogs() {
      // First filter by search username
      let filteredLogs = this.logs.filter(log => 
        log.performed_by.toLowerCase().includes(this.searchUsername.toLowerCase())
      );

      // Then sort based on the selected order
      return filteredLogs.sort((a, b) => {
        const dateA = new Date(a.timestamp);
        const dateB = new Date(b.timestamp);
        return this.isSortedAsc ? dateA - dateB : dateB - dateA;
      });
    }
  },
  mounted() {
    this.fetchLogs();
    this.fetchRrLogs();
  },
  methods: {
    async fetchRrLogs() {
      try {
        const response = await axios.get('/api/rrtransaction-logs');

        this.rrLogs = response.data.data.map(log => {
          if (typeof log.transaction_details === 'string') {
            try {
              log.transaction_details = JSON.parse(log.transaction_details);
            } catch (e) {
              console.error('Invalid JSON in transaction_details:', log.transaction_details);
            }
          }
          return log;
        });
      } catch (error) {
        console.error('Failed to fetch RR logs:', error);
      }
    },
    toggleSort() {
      this.isSortedAsc = !this.isSortedAsc;
    },
    async fetchLogs() {
      try {
        const response = await axios.get('/api/transaction-logs');
        this.logs = response.data.data.map(log => {
          if (typeof log.transaction_details === 'string') {
            try {
              log.transaction_details = JSON.parse(log.transaction_details);
            } catch (e) {
              console.error('Invalid JSON in transaction_details:', log.transaction_details);
            }
          }
          return log;
        });
      } catch (error) {
        console.error('Failed to fetch logs:', error);
      }
    },
    formatDate(datetime) {
      const date = new Date(datetime);
      return date.toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      });
    },
    formatTime(datetime) {
      const date = new Date(datetime);
      return date.toLocaleTimeString(undefined, {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
      });
    },
    openModal(log) {
      this.selectedLog = log;  // Set selected log for modal details
      console.log(this.selectedLog.transaction_details);  // Log the full transaction_details
      console.log(this.selectedLog.transaction_details.return_receipt);  // Log return_receipt specifically
      console.log(this.selectedLog.transaction_details.return_receipt?.return_proof);  // Log return_proof specifically
      
      this.isModalOpen = true;  // Open the modal
    },
    closeModal() {
      this.isModalOpen = false;  // Close the modal
      this.selectedLog = null;   // Clear selected log
    },
    async deleteLog(logId) {
      try {
        // Show confirmation dialog using SweetAlert2
        const result = await Swal.fire({
          title: 'Are you sure?',
          text: 'Do you want to delete this log?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, delete it!',
          cancelButtonText: 'Cancel',
        });

        if (result.isConfirmed) {
          // Make DELETE API call to delete the log
          await axios.delete(`/api/transaction-logs/${logId}`);
          
          // Show success message
          Swal.fire('Deleted!', 'The log has been deleted.', 'success');
          
          // Refresh the logs after deletion
          this.fetchLogs();
        }
      } catch (error) {
        console.error('Error deleting log:', error);
        Swal.fire('Error!', 'Failed to delete the log. Please try again.', 'error');
      }
    },
  },
};
</script>