<template>
  <div class="flex items-center gap-3">
    <!-- Search Bar with Button -->
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

    <!-- Threshold Filter -->
    <select
      id="threshold"
      v-model="selectedStocks"
      @change="applyFilters"
      class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer w-32"
    >
      <option value="">All Stock Level</option>
      <option value="Low">Low</option>
      <option value="Normal">Normal</option>
      <option value="High">High</option>
    </select>

    <!-- Date Added Filter -->
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

    <!-- Last Update Filter -->
    <div class="flex items-center gap-2">
      <label for="last_update" class="text-sm text-gray-700">Last Update</label>
      <input
        id="last_update"
        type="date"
        v-model="selectedLastUpdate"
        @change="applyFilters"
        class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer w-36"
      />
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