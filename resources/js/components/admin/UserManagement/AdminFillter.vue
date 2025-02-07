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

    <!-- Role Filter -->
    <select
      v-model="selectedRole"
      @change="applyFilters"
      class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
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
      class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
    >
      <option value="">All Status</option>
      <option value="1">Active</option>
      <option value="0">Inactive</option>
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
  methods: {
    applySearch() {
      this.$emit("search", this.tempSearchQuery);
    },
    applyFilters() {
      this.$emit("filter", {
        role: this.selectedRole,
        status: this.selectedStatus
      });
    }
  }
};
</script>