<template>
  <div class="md:flex md:items-center gap-4 ">
    <!-- Search Bar with Button -->
    <div class="flex items-center gap-2">
      <input
        v-model="tempSearchQuery"
        type="text"
        placeholder="Search by name..."
        class="dark:bg-custom-table border dark:focus:ring-white border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-[150px]"
      />

    </div> <br>

    <!-- Role Filter -->
    <select
      v-model="selectedRole"
      @change="applyFilters"
      class="mr-5 w-[240px] md:w-[148px] mb-5 sm:mb-5 md:mb-0 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-custom-table dark:focus:ring-white"
    >
      <option value="">All Roles</option>
      <option value="admin">Admin</option>
      <option value="manager">Manager</option>
      <option value="procurement">Procurement</option>
      <option value="warehouse_staff">Warehouse Staff</option>
    </select>
    <!-- Status Filter -->
    <select
      v-model="selectedStatus"
      @change="applyFilters"
      class="w-[240px] md:w-[148px] borderborder-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-custom-table dark:focus:ring-white "
    >
      <option value="">All Status</option>
      <option value="active">active</option>
      <option value="inactive">inactive</option>
    </select>
  </div>
</template>

<script>
export default {
  data() {
    return {
      tempSearchQuery: "", // Temporary search input
      selectedRole: "",
      selectedStatus: ""
    };
  },
  watch:{
    tempSearchQuery(newQuery){
      this.$emit("search", newQuery)
    }
  },
  methods: {
    
    applyFilters() {
      console.log("Filtering by status:", this.selectedStatus); // Debugging line
      this.$emit("filter", {
        role: this.selectedRole,
        status: this.selectedStatus
      });
    }
  }
};
</script>