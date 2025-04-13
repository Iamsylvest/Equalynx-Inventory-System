<template>
  <div class="mt-10 font-roboto">
    <!-- Add User Component -->
    <div class="flex flex-row justify-between items-center flex-wrap mb-3">
      <h1 class="text-lg">Materials ( <span> {{ total }} </span> )</h1>
      <div class="static flex items-center justify-end space-x-6">
        <InventoryFillter @search="updateSearch" @filter="updateFilter"/>
            <!-- Sorting Dropdown -->
            <div class="flex items-center gap-2 mt-5">
              <select 
                v-model="sortKey" 
                @change="sortBy(sortKey)"
                class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 dark:bg-custom-table dark:focus:ring-white focus:ring-blue-500 w-full sm:w-[150px] md:w-[150px] h-[35px]"
              >
                <option disabled value="">Sort by:</option>
                <option value="material_name">Item</option>
                <option value="created_at">Date Added</option>
                <option value="updated_at">Last update</option>
                <option value="stocks">Stocks</option>
              </select>

              <!-- Sort Order Toggle (Asc/Desc) -->
              <button
                @click="toggleSortOrder"
                class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 dark:bg-custom-table dark:focus:ring-white focus:ring-blue-500"
              >
                {{ sortOrder === 'asc' ? '▲' : '▼' }}
              </button>
            </div>

            <AddMaterial v-if="userRole === 'warehouse_staff'" @materialAdded="handleMaterialAdded"/>
      </div>
    </div>

    

    <!-- Table for Materials -->
    <table class="table-auto w-full botr border-collapse mt-1 shadow-lg">
      <thead class="h-14 bg-custom-blue text-white  dark:bg-custom-table dark:border-b ">
        <tr class="bg-custom-blue text-white">
          <th class="px-4 py-2 border-0 text-center font-bold dark:bg-custom-table">Material Name</th>
          <th class="px-4 py-2 border-0 text-center font-bold dark:bg-custom-table">Stocks</th>
          <th class="px-4 py-2 border-0 text-center font-bold dark:bg-custom-table">Measurement</th>
          <th class="px-4 py-2 border-0 text-center font-bold dark:bg-custom-table">Threshold</th>
          <th class="px-4 py-2 border-0 text-center font-bold dark:bg-custom-table">Date Added</th>
          <th class="px-4 py-2 border-0 text-center font-bold dark:bg-custom-table">Last Update</th>
          <th class="px-4 py-2 border-0 text-center font-bold dark:bg-custom-table" v-if="userRole === 'warehouse_staff'" >Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="filteredMaterials.length === 0">
          <td colspan="6" class="text-center py-4">No materials found</td>
        </tr>
        <tr 
              v-for="(material, index) in filteredMaterials" 
              :key="material.id" 
              :class="{
                'border-b border-b-gray-400': true,
                'bg-white hover:bg-gray-200': material.stocks > material.threshold,  // Apply white background if stock > threshold
                'bg-red-300 hover:bg-red-400 dark:bg-red-300 dark:hover:bg-red-400 low-stock': material.stocks <= material.threshold,  // Apply red background if stock <= threshold
              }"
            >
          <td class="text-center px-4 py-2 border-0 ">{{ material.material_name }}</td>
          <td class="px-4 py-4 text-center">{{ material.stocks }}</td>
          <td class="text-center px-4 py-2 border-0">{{ Math.floor(material.measurement_quantity) }} {{ material.measurement_unit }}</td>
          <td class="px-4 py-4 text-center">{{ material.threshold }}</td>
          <td class="text-center px-4 py-2 border-0">{{ formatDate(material.created_at) }}</td>
          <td class="text-center px-4 py-2 border-0">{{ formatDate(material.updated_at) }}</td>
          <td class="text-center px-4 py-2 border-0 space-x-3 " v-if="userRole === 'warehouse_staff'" >

            <button class="text-gray-500 hover:underline w-full sm:w-auto" v-if="userRole === 'warehouse_staff'" @click="showEditModal(material) ">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487 18.5 2.75a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
              </svg>
            </button>
            <button @click="deleteMaterial(material.id)" v-if="userRole === 'warehouse_staff'"  class="text-gray-500 hover:underline">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
              </button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="flex items-center justify-center py-2 px-4 bg-white shadow-md dark:bg-custom-table z-50">
              <!-- Previous Button -->
              <button 
                @click="fetchMaterials(currentPage - 1)" 
                :disabled="currentPage === 1" 
                class="text-lg px-4 py-2 rounded-lg disabled:opacity-2 hover:bg-gray-100 hover:dark:bg-custom-hover"
              >
                ←
              </button>

              <!-- Pagination Numbers -->
              <span 
                v-for="page in lastPage" 
                :key="page"
                @click="fetchMaterials(page)"
                class="mx-2 px-3 py-2 cursor-pointer rounded-lg  dark:bg-custom-table"
                :class="{'bg-blue-500 text-white': currentPage === page, 'hover:bg-gray-200': currentPage !== page}"
              >
                {{ page }}
              </span>

              <!-- Next Button -->
              <button 
                @click="fetchMaterials(currentPage + 1)" 
                :disabled="currentPage === lastPage" 
                class="text-lg px-4 py-2 rounded-lg disabled:opacity-2 hover:bg-gray-100  hover:dark:bg-custom-hover"
              >
                →
              </button>
        </div>

         <!-- Edit Material Modal -->
          <EditMaterial
            v-if="isEditModalVisible"
            :isVisible="isEditModalVisible"
            :material="selectedMaterial"
            @update="updateMaterial"
            @closeModal="closeEditModal"
            @start-loading="loading = true"
            :loading="loading"
          />

  </div>
</template>

  <script>
  import Swal from 'sweetalert2';
  import InventoryFillter from '@/components/admin/Inventory/InventoryFillter.vue';
  import EditMaterial from '@/components/admin/Inventory/EditMaterial.vue';
  import AddMaterial from '@/components/admin/Inventory/AddMaterial.vue';
  import { mapGetters } from "vuex";  
  import axios from 'axios';

  export default {
  components: {
    InventoryFillter,
    AddMaterial,
    EditMaterial
  },
  data() {
    return {
      materials: [],
      total: 0,
      tempSearchQuery: '',
      selectedStocks: '',
      selectedDateAdded: '',
      selectedLastUpdate: '',
      selectedMaterial: null, // Store selected material
      isEditModalVisible: false, // To show/hide edit modal
      threshold: '',
      loading: false,

          // Sorting
      sortKey: '',
      sortOrder: 'asc',
    };
  },
  mounted() {
    this.fetchMaterials();

  },
  computed: {
    ...mapGetters('auth', ['user', 'userRole']),
    filteredMaterials() {
      if (!this.materials) return []; // Prevent null reference error

      // Filter the materials based on the search query, stocks, and date filters
      let results = this.materials.filter(material => {
        if (!material || !material.material_name) return false; // Ensure material is valid

        const materialName = material.material_name.toLowerCase();
        const matchesMaterialName = this.tempSearchQuery
          ? materialName.includes(this.tempSearchQuery.toLowerCase())
          : true;
        const matchesStocks = this.selectedStocks
          ? this.isStockLevelMatch(material.stocks, this.selectedStocks)
          : true;
        // Get `YYYY-MM-DD` from material.created_at and updated_at
        const createdAt = material.created_at ? material.created_at.split('T')[0] : null;
        const updatedAt = material.updated_at ? material.updated_at.split('T')[0] : null;

        const matchesDateAdded = this.selectedDateAdded
          ? createdAt === this.selectedDateAdded
          : true;

        const matchesLastUpdate = this.selectedLastUpdate
          ? updatedAt === this.selectedLastUpdate
          : true;

        return matchesMaterialName && matchesStocks && matchesDateAdded && matchesLastUpdate;
      });

      // Apply sorting if sortKey is set
      if (this.sortKey) {
        results.sort((a, b) => {
          let modifier = this.sortOrder === 'asc' ? 1 : -1;

          // Handle sorting by date fields (created_at, updated_at)
          if (this.sortKey === 'created_at' || this.sortKey === 'updated_at') {
            return (new Date(a[this.sortKey]) - new Date(b[this.sortKey])) * modifier;
          }

          // Handle sorting by numeric fields like stocks
          if (typeof a[this.sortKey] === 'number') {
            return (a[this.sortKey] - b[this.sortKey]) * modifier;
          }

          // Handle sorting by string fields (e.g., material_name)
          return a[this.sortKey]?.localeCompare(b[this.sortKey]) * modifier;
        });
      }

      return results;
    },
  },
  methods: {

    sortBy(key) {
  if (this.sortKey === key) {
    // If the same key is clicked again, toggle the sort order (asc/desc)
    this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
  } else {
    // If a new key is clicked, set it as the sort key and set order to ascending
    this.sortKey = key;
    this.sortOrder = 'asc';
  }
  // After updating the sortKey and sortOrder, sort the materials
  this.sortMaterials();
},

sortMaterials() {
  this.materials.sort((a, b) => {
    let valueA = a[this.sortKey];
    let valueB = b[this.sortKey];

    // If sorting by 'created_at', convert to Date objects
    if (this.sortKey === 'created_at') {
      valueA = new Date(a[this.sortKey]);
      valueB = new Date(b[this.sortKey]);
    }

    // String comparison for alphabetic sorting (e.g., material_name)
    if (typeof valueA === 'string' && typeof valueB === 'string') {
      return valueA.localeCompare(valueB) * (this.sortOrder === 'asc' ? 1 : -1);
    }

    // Numeric comparison (e.g., for stocks, quantities)
    if (typeof valueA === 'number' && typeof valueB === 'number') {
      return (valueA - valueB) * (this.sortOrder === 'asc' ? 1 : -1);
    }

    // Default comparison if types are different or unsupported
    if (valueA < valueB) return this.sortOrder === 'asc' ? -1 : 1;
    if (valueA > valueB) return this.sortOrder === 'asc' ? 1 : -1;
    return 0;
  });
},



    handleMaterialAdded(newMaterial) {
    this.materials.push(newMaterial);
    this.materials.total++
    this.isEditModalVisible = false; // Close modal
    },



    deleteMaterial(materialId) {
    if (this.userRole !== 'warehouse_staff') {
        Swal.fire("Permission Denied", "You are not authorized to delete materials.", "error");
        return;
    }

    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!"
          }).then((result) => {
              if (result.isConfirmed) {
                  axios.delete(`/api/inventory/${materialId}`).then(() => {
                      // Update the frontend
                      this.materials = this.materials.filter(material => material.id !== materialId); // Filter out the deleted material
                      this.total--; // Decrement the total count
                      Swal.fire("Deleted!", "The material has been deleted.", "success");
                  })
                  .catch(error => {
                      console.error("Error deleting material:", error);
                      Swal.fire("Error!", "Something went wrong.", "error");
                  });
              }
          });
      },

    closeEditModal(){
      console.log("Close Edit Modal triggered");
      this.isEditModalVisible = false;
      this.selectedMaterial = null;
    },
    
    showEditModal(material) {
      console.log("Editing Material:", material); // ✅ Check if this runs
      this.selectedMaterial = material;
      this.isEditModalVisible = true; // ✅ Now modal will open properly
    }, 
    
    // Handle the update event from the EditMaterial component
    updateMaterial(updatedMaterial) {
      this.loading = true; // Start loading
      axios.patch(`/api/inventory/${updatedMaterial.id}`, updatedMaterial)
        .then(() => {
          // Find the index of the updated material in the array
          const index = this.materials.findIndex(material => material.id === updatedMaterial.id);
          if (index !== -1) {
            // Replace the old material with the updated one
            this.materials[index] = updatedMaterial;
            // Force reactivity by creating a new array
            this.materials = [...this.materials];
          }

          // Re-fetch materials to ensure the UI syncs with the server
          this.fetchMaterials(this.currentPage);
          
          // Close the modal and reset the selected material
          this.isEditModalVisible = false;
          this.selectedMaterial = null;

          Swal.fire("Success!", "Material updated successfully.", "success");
        })
        .catch((error) => {
          console.error("Error updating material:", error.response.data);
          Swal.fire("Error!", `Failed to update material: ${error.response.data.message}`, "error");
        })
        .finally(() => {
          this.loading = false; // Stop loading in both success & error cases
        });
    },

    // Add the 'isStockLevelMatch' method
    isStockLevelMatch(stocks, selectedLevel) {
      // Aligning with the backend logic for stock levels
      if (selectedLevel === 'Low' && stocks <= 20) return true;  // Backend uses <= 20 for Low
      if (selectedLevel === 'Normal' && stocks > 20 && stocks <= 40) return true;  // Backend uses > 20 AND <= 40 for Normal
      if (selectedLevel === 'High' && stocks > 40) return true;  // Backend uses > 40 for High
      return false;
    },


    async fetchMaterials(page = 1) {
      try {
        const params = new URLSearchParams();
        params.append('page', page);

        if (this.tempSearchQuery !== '') {
          params.append('search', this.tempSearchQuery);
        }
        if (this.selectedStocks !== '') {
          params.append('stocks', this.selectedStocks);
        }
        if (this.selectedDateAdded !== '') {
            const formattedDate = new Date(this.selectedDateAdded).toISOString().split('T')[0];
            params.append('created_at', formattedDate);
            console.log("📅 Filtering by Date Added:", formattedDate);
        }
        if (this.selectedLastUpdate !== '') {
            const formattedUpdate = new Date(this.selectedLastUpdate).toISOString().split('T')[0];
            params.append('updated_at', formattedUpdate);
            console.log("📅 Filtering by Last Update:", formattedUpdate);
        }

        const url = `/api/inventory?${params.toString()}`;
        console.log("Fetching data from:", url); // ✅ Check if dates are included

        const response = await axios.get(url);
        this.materials = response.data.data;
        this.total = response.data.total;
        this.currentPage = response.data.current_page;
        this.lastPage = response.data.last_page;
      } catch (error) {
        console.error('Error fetching materials:', error);
      }
    },

    updateSearch(query) {
      this.tempSearchQuery = query;
      this.fetchMaterials(1); 
    },

    updateFilter(filters) {
      this.selectedStocks = filters.stocks || ''; 
      this.selectedDateAdded = filters.created_at || '';  
      this.selectedLastUpdate = filters.updated_at ||''; 
      this.tempSearchQuery = filters.material_name || '';

      this.currentPage = 1;  // Reset to the first page when applying filters
      this.fetchMaterials(this.currentPage);  // Fetch materials for the first page with new filters
    },

    formatDate(date) {
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    },
   
  }
};
</script>