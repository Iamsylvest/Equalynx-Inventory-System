<template>
  <div class="flex items-center gap-4 ">
    <!-- Search Bar with Button -->
    <div class="flex items-center gap-2">
      <input
        v-model="tempSearchQuery"
        type="text"
        placeholder="Search by name..."
        class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
      />
      <button
        @click="applySearch"
        class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-400 transition"
      >
        Search
      </button>
    </div>

    <!-- Meterials Filter -->
    <select
      v-model="selectedRole"
      @change="applyFilters"
      class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
    >
      <option value="">All Materials</option>
      <option value="">none</option>
      <option value="">none</option>
      <option value="">none</option>
      <option value="">none</option>
    </select>

      
      <!-- Threshold Filter -->
    <select
      id="threshold"
      v-model="selectedThreshold"
      @change="applyFilters"
      class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
    >
      <option value="">All Stock Level</option>
      <option value="low">Low Stock</option>
      <option value="medium">Medium Stock</option>
      <option value="high">High Stock</option>
    </select>

        <!-- Date Added Filter -->
      <label for="date_added" class="block text-md font-medium text-gray-700">Date Added</label>
      <input
        id="date_added"
        type="date"
        v-model="selectedDateAdded"
        @change="applyFilters"
        class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
      />

      <!-- Last Update Filter -->
      <label for="last_update" class="block text-mdfont-medium text-gray-700">Last Update</label>
      <input
        id="last_update"
        type="date"
        v-model="selectedLastUpdate"
        @change="applyFilters"
        class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
      />




  </div>
</template>

<script>


</script>