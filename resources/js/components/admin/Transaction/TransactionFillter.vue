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

  <div class="grid grid-cols-2 grid-rows-3 sm:flex md:flex mt-5 min-w-auto h-auto gap-2 mb-2">
    <div class="col-span-2 mt-5 sm:mt-0 md:mt-0 lg:mt-0 flex items-center gap-2">
    <input
        v-model="tempSearchQuery"
        type="text"
        placeholder="Search by name..."
        class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-custom-table dark:focus:ring-white w-full h-[35px] sm:w-40 md:w-40"
    />
  </div>

    <div class="col-span-2 mt-5 sm:mt-0 md:mt-0 lg:mt-0 flex items-center gap-2 w-[250px] sm:w-full md:w-full">
      <!-- Status Filter  -->
      <select
        v-model="selectedStatus"
        @change="applyFilters"
        class="w-[250px] mt-2 sm:mt-0 md:mt-0 lg:mt-0 border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer sm:w-32 md:w-32  dark:bg-custom-table dark:focus:ring-white h-[35px]"
      >
        <option value="">All Status</option>
        <option value="approved">approved</option>
        <option value="pending">pending</option>
        <option value="rejected">rejected</option>
      </select>
    </div>

    <div class="col-span-2 w-[250px] mt-5 sm:mt-0 md:mt-0 lg:mt-0 flex items-center gap-2">
      <div class="col-span-2 w-[250px]">
        <div class="flex items-center gap-2 sm:mt-0 md:mt-0 lg:mt-0">
          <label for="date_added" class="text-sm text-gray-700 dark:text-custom-white">Date Added</label>
            <input
              id="date_added"
              type="date"
              v-model="selectedDateAdded"
              @change="applyFilters"
              class="custom-date-input border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer w-full h-[35px] sm:w-36 md:w-36  dark:bg-custom-table dark:focus:ring-white"
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
      tempSearchQuery: "",
      selectedStatus: '',
      selectedDateAdded: '',
    };
  },
  watch:{
       // This will emit 'search' every time tempSearchQuery changes (when user types)
      tempSearchQuery(newQuery){
        this.$emit("search",newQuery)
      }
  },
  methods: {
    applyFilters() {
      console.log("✅ Applying Filters - Status:", this.selectedStatus, "Date:", this.selectedDateAdded);
      this.$emit("updateFilters", {
        status: this.selectedStatus,
        created_at: this.selectedDateAdded,
      });
    },
  },
};
</script>