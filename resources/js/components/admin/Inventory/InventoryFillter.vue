<template>
   <div class="md:flex md:items-center gap-3">
    <!-- Search Bar with Button 
    <div class="mt-5 sm:mt-0 md:mt-0 lg:mt-0 flex items-center gap-2">
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

    -- Threshold Filter 
    <select
      id="threshold"
      v-model="selectedStocks"
      @change="applyFilters"
      class="mt-5 sm:mt-0 md:mt-0 lg:mt-0 border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer w-full sm:w-32 md:w-32"
    >
      <option value="">All Stock Level</option>
      <option value="Low">Low</option>
      <option value="Normal">Normal</option>
      <option value="High">High</option>
    </select>


    -- Date Added Filter 
    <div class="flex items-center gap-2 mt-5 sm:mt-0 md:mt-0 lg:mt-0">
      <label for="date_added" class="text-sm text-gray-700 w-full">Date Added</label>
      <input
        id="date_added"
        type="date"
        v-model="selectedDateAdded"
        @change="applyFilters"
        class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer w-full sm:w-36 md:w-36"
      />
    </div>

    -- Last Update Filter 
    <div class="flex items-center gap-2 mt-5 sm:mt-0 md:mt-0 lg:mt-0">
      <label for="last_update" class="text-sm text-gray-700 w-full">Last Update</label>
      <input
        id="last_update"
        type="date"
        v-model="selectedLastUpdate"
        @change="applyFilters"
        class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer w-full sm:w-36 md:w-36"
      />
    </div> -->

    <div class="grid grid-cols-2 grid-rows-4 sm:flex md:flex mt-5 min-w-auto h-auto gap-2">

      <div class="col-span-2 mt-5 sm:mt-0 md:mt-0 lg:mt-0 flex items-center gap-2">
        <input
        v-model="tempSearchQuery"
        type="text"
        placeholder="Search by name..."
        class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-40"
      />
      <button
        @click="applySearch"
        class="w-full px-3 py-1 bg-blue-500 text-white text-sm font-semibold rounded-md hover:bg-blue-400 transition ml-2"
      >
        Search
      </button>
      </div>

      <div class="col-span-2 ">
        <select
          id="threshold"
          v-model="selectedStocks"
          @change="applyFilters"
          class="w-[250px] mt-2 sm:mt-0 md:mt-0 lg:mt-0 border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer sm:w-32 md:w-32"
        >
          <option value="">All Stock Level</option>
          <option value="Low">Low</option>
          <option value="Normal">Normal</option>
          <option value="High">High</option>
        </select>
      </div>

      <div class="col-span-2 w-[250px]">
        <div class="flex items-center gap-2 sm:mt-0 md:mt-0 lg:mt-0">
          <label for="date_added" class="text-sm text-gray-700 w-full">Date Added</label>
          <input
            id="date_added"
            type="date"
            v-model="selectedDateAdded"
            @change="applyFilters"
            class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer w-full sm:w-36 md:w-36"
          />
        </div>
      </div>

      <div class="grid-col-span-2 w-[250px]">
        <div class="flex items-center gap-2 sm:mt-0 md:mt-0 lg:mt-0 ">
          <label for="last_update" class="text-sm text-gray-700 ">Last Update</label>
          <input
            id="last_update"
            type="date"
            v-model="selectedLastUpdate"
            @change="applyFilters"
            class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer w-full sm:w-36 md:w-36"
          />
        </div>
      </div>

      


      

      
    </div>
  </div> 
</template>

<script>



export default {
  data () {
    return{
      tempSearchQuery: "",
      selectedMaterial: "",
      selectedStocks: "",
      selectedDateAdded: "",
      selectedLastUpdate: "",
    }
  },
  methods: {
    applySearch () {
      this.$emit("search", this.tempSearchQuery)
    },
    applyFilters () {
      this.$emit( "filter",{

        stocks: this.selectedStocks, 
        created_at: this.selectedDateAdded, 
        updated_at: this.selectedLastUpdate, 
       })
     
    }
},

}

</script>