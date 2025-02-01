<template>
   
    <div class="p-8 ">
     <header class="flex flex-row justify-between items-center">
         <h1 class="text-2xl  whitespace-nowrap">Activity Logs</h1>
         <div class="flex items-end justify-end">
           <ActivityLogsNotification />
           <ActivityLogsProfile />
         </div>
     </header>
  
     <div class="flex flex-wrap items-center justify-between relative top-10 w-full">
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

      <button
      class="p-3 px-12 text-md font-bold border-b-2 shadow-md rounded-md focus:outline-none w-full sm:w-auto"
        @click="showTable = 'inventory'"
        :class="{
          'bg-custom-blue text-white': showTable === 'inventory',
          'hover:bg-custom-blue hover:text-white': showTable !== 'inventory'
        }"
      >
        Inventory
      </button>
    </div>
             <div class="flex items-center space-x-4 ml-auto">
                <ActivityLogsSearch Class="w-full sm:w-auto"  />
                <ActivityLogsFillter Class="w-full sm:w-auto"  />  
             </div>
     </div>

     <div class="flex-wrap gap-2 md:gap-8 border-2 px-5 mb-8 mt-16 shadow-md ">
      <div class="flex flex-wrap gap-4 md:flex-nowrap mt-5 mb-5 ">
        <div class="flex-grow w-full md:w-auto">
          <label for="start-date" class="whitespace-nowrap text-md font-medium text-gray-700">Start Date:</label>
          <input
            type="date"
            id="start-date"
            v-model="selectedStartDate"
            class="mt-1 border-2 px-4 py-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm w-full md:w-auto"
          />
        </div>

        <div class="flex-grow w-full md:w-auto">
          <label for="end-date" class="whitespace-nowrap text-md font-medium text-gray-700">End Date:</label>
          <input
            type="date"
            id="end-date"
            v-model="selectedStartEnd"
            class="mt-1 border-2 px-4 py-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm w-full md:w-auto"
          />
        </div>

        <div class="flex-grow w-full md:w-auto">
          <button class="mt-1 px-6 py-2 rounded-sm bg-green-300 hover:bg-green-400 w-full md:w-auto">
            Search
          </button>
        </div>

        <div class="flex-grow w-full md:w-auto">
          <select class="border-2 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-auto">
            <option value="" disabled selected>Select Activity Type</option>
            <option value="option1">Option 1</option>
            <option value="option2">Option 2</option>
            <option value="option3">Option 3</option>
          </select>
        </div>
      </div>
    </div>

    <div class="mt-8">
        <DeliveryReceipt v-if="showTable === 'delivery'" class="w-full sm:w-auto" />
        <ReturnReceipt v-if="showTable === 'return'" class="w-full sm:w-auto" />
        <Inventory v-if="showTable === 'inventory'" class="w-full sm:w-auto" />
      </div>
 
 </div>
 
 
 
 </template>
 

<script>
import { mapGetters } from 'vuex'; // ✅ Import mapGetters
import ActivityLogsFillter from '../../components/admin/ActivityLogs/ActivityLogsFillter.vue';
import ActivityLogsNotification from '../../components/admin/ActivityLogs/ActivityLogsNotification.vue';
import ActivityLogsProfile from '../../components/admin/ActivityLogs/ActivityLogsProfile.vue';
import ActivityLogsSearch from '../../components/admin/ActivityLogs/ActivityLogsSearch.vue';
import DeliveryReceipt from '../../components/admin/ActivityLogs/DeliveryReceipt.vue';
import Inventory from '../../components/admin/ActivityLogs/Inventory.vue';
import ReturnReceipt from '../../components/admin/ActivityLogs/ReturnReceipt.vue';

export default {
    components :{
        ActivityLogsFillter,
        ActivityLogsNotification,
        ActivityLogsProfile,
        ActivityLogsSearch,
        DeliveryReceipt,
        Inventory,
        ReturnReceipt,
 
    },
    data() {
      return {
        showTable: 'delivery' // Default value set to 'delivery'
      };
    },
    computed: {
      ...mapGetters(['isAuthenticated']) // Map the Vuex getter
    },

};
    
</script>