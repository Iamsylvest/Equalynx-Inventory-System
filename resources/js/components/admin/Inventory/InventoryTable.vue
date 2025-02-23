<template>
  <div class="mt-10 font-roboto">
    <!-- Add User Component -->
    <div class="flex flex-row justify-between items-center flex-wrap relative top-[-10px] mb-3">
      <h1 class="text-lg">Materials ( <span> {{ total }} </span> )</h1>
      <div class="flex items-center justify-end space-x-6">
        <InventoryFillter @search="updateSearch" @filter="updateFilter"/>
        <AddMaterial v-if="userRole === 'warehouse_staff'" @materialAdded="handleMaterialAdded"/>
      </div>
    </div>

    <!-- Table for Materials -->
    <table class="table-auto w-full border-collapse mt-1 shadow-lg">
      <thead class="h-14">
        <tr class="bg-custom-blue text-white">
          <th class="px-4 py-2 border-0 text-center font-bold">Material Name</th>
          <th class="px-4 py-2 border-0 text-center font-bold">Stocks</th>
          <th class="px-4 py-2 border-0 text-center font-bold">Measurement</th>
          <th class="px-4 py-2 border-0 text-center font-bold">Date Added</th>
          <th class="px-4 py-2 border-0 text-center font-bold">Last Update</th>
          <th class="px-4 py-2 border-0 text-center font-bold" v-if="userRole === 'warehouse_staff'" >Action</th>
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
                'border-b border-b-gray-400': true,   /* Always apply bottom border */
                'bg-white hover:bg-gray-200': material.stocks > 20 && index % 2 === 0 || material.stocks > 20 && index % 2 !== 0,  /* Even rows - Gray */
                'bg-red-200 hover:bg-red-300': material.stocks <= 20  /* Low stock - Always Red */
              }"
            >
          <td class="text-center px-4 py-2 border-0">{{ material.material_name }}</td>
          <td class="px-4 py-4 text-center">{{ material.stocks }}</td>
          <td class="text-center px-4 py-2 border-0">{{ Math.floor(material.measurement_quantity) }} {{ material.measurement_unit }}</td>
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
    <div class="flex items-center justify-center py-2 px-4 bg-white shadow-md z-50">
              <!-- Previous Button -->
              <button 
                @click="fetchMaterials(currentPage - 1)" 
                :disabled="currentPage === 1" 
                class="text-lg px-4 py-2 rounded-lg disabled:opacity-2 hover:bg-gray-100"
              >
                ←
              </button>

              <!-- Pagination Numbers -->
              <span 
                v-for="page in lastPage" 
                :key="page"
                @click="fetchMaterials(page)"
                class="mx-2 px-3 py-2 cursor-pointer rounded-lg"
                :class="{'bg-blue-500 text-white': currentPage === page, 'hover:bg-gray-200': currentPage !== page}"
              >
                {{ page }}
              </span>

              <!-- Next Button -->
              <button 
                @click="fetchMaterials(currentPage + 1)" 
                :disabled="currentPage === lastPage" 
                class="text-lg px-4 py-2 rounded-lg disabled:opacity-2 hover:bg-gray-100"
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
    

    };
  },
  mounted() {
    this.fetchMaterials();
  },
  computed: {
    ...mapGetters('auth', ['user', 'userRole']),
    filteredMaterials() {
      if (!this.materials) return []; // Prevent null reference error

      return this.materials.filter(material => {
        if (!material || !material.material_name) return false; // Ensure material is valid

        const materialName = material.material_name.toLowerCase();
        const matchesMaterialName = this.tempSearchQuery
          ? materialName.includes(this.tempSearchQuery.toLowerCase())
          : true;
        const matchesStocks = this.selectedStocks
          ? this.isStockLevelMatch(material.stocks, this.selectedStocks)
          : true;
        const matchesDateAdded = this.selectedDateAdded
          ? this.formatDate(material.created_at) === this.selectedDateAdded
          : true;
        const matchesLastUpdate = this.selectedLastUpdate
          ? this.formatDate(material.updated_at) === this.selectedLastUpdate
          : true;

        return matchesMaterialName && matchesStocks && matchesDateAdded && matchesLastUpdate;
      });
    },
  },
  methods: {

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
          const formattedDate = this.formatDate(this.selectedDateAdded);
          params.append('created_at', formattedDate);
          console.log("Filtering by Date Added:", formattedDate);
        }
        if (this.selectedLastUpdate !== '') {
          const formattedUpdate = this.formatDate(this.selectedLastUpdate);
          params.append('updated_at', formattedUpdate);
          console.log("Filtering by Last Update:", formattedUpdate);
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
      this.selectedDateAdded = filters.created_at ? this.formatDate(filters.created_at) : '';  
      this.selectedLastUpdate = filters.updated_at ? this.formatDate(filters.updated_at) : ''; 
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
    }
  }
};
</script>