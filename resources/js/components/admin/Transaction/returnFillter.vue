<template>
    <!-- <div class="flex items-center gap-3">
       Search Bar with Button 
      <div class="flex items-center gap-2">
        <input
          v-model="tempSearchQuery"
          type="text"
          placeholder="Search by name..."
          class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-40"
        />
        <button
          @click="applySearch"
          class="px-3 py-1 bg-blue-500 text-white text-sm font-semibold rounded-md hover:bg-blue-400 transition"
        >
          Search
        </button>
      </div>
  
      Status Filter 
      <select
        v-model="selectedStatus"
        @change="applyFilters"
        class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        <option value="">All Status</option>
        <option value="approved">approved</option>
        <option value="pending">pending</option>
        <option value="rejected">rejected</option>
      </select>
  
       Date Added Filter 
      <div class="flex items-center gap-2">
        <label for="date_added" class="text-sm text-gray-700">Date Added</label>
        <input
          id="date_added"
          type="date"
          v-model="selectedDateAdded"
          @change="applyFilters"
          class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer w-36"
        />
      </div>
    </div> -->
  
    <div class="grid grid-cols-2 grid-rows-4 sm:flex md:flex mt-5 min-w-auto h-auto gap-2">
      <div class="col-span-2 mt-5 sm:mt-0 md:mt-0 lg:mt-0 flex items-center gap-2">
        <input
          v-model="tempSearchQueryReturn"
          type="text"
          placeholder="Search by name..."
          class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-40 dark:bg-custom-table dark:focus:ring-white"
        />
      </div>
  
      <div class="col-span-2 ">
        <!-- Status Filter  -->
        <select
          v-model="selectedStatusReturn"
          @change="applyFiltersReturn"
          class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none 
                  focus:ring-2 focus:ring-blue-500 cursor-pointer w-full sm:w-36 
                  dark:bg-custom-table dark:focus:ring-white custom-date-input"
        >
          <option value="">All Status</option>
          <option value="approved">approved</option>
          <option value="pending">pending</option>
          <option value="rejected">rejected</option>
        </select>
      </div>
  
      <div class="col-span-2 w-[250px]">
        <div class="col-span-2 w-[250px]">
          <div class="flex items-center gap-2 sm:mt-0 md:mt-0 lg:mt-0">
            <label for="date_added" class="text-sm text-gray-700 dark:text-custom-white">Date Added</label>
              <input
                id="date_added"
                type="date"
                v-model="selectedDateAddedReturn"
                @change="applyFiltersReturn"
                class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none 
                  focus:ring-2 focus:ring-blue-500 cursor-pointer w-full sm:w-36 
                  dark:bg-custom-table dark:focus:ring-white custom-date-input"
              />
          </div>
        </div>
      </div>
  
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        tempSearchQueryReturn: "",
        selectedStatusReturn: '',
        selectedDateAddedReturn: '',
      };
    },
    watch:{
      tempSearchQueryReturn(newQuery){
        this.$emit("searchReturn", newQuery)
      }
    },
    methods: {
      applyFiltersReturn() {
        console.log("✅ Applying Filters - Status:", this.selectedStatusReturn, "Date:", this.selectedDateAddedReturn);
        this.$emit("updateFiltersReturn", {
          status: this.selectedStatusReturn,
          created_at: this.selectedDateAddedReturn,
        });
      },
    },
  };
  </script>