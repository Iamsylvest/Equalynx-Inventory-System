<template>
    <div class="font-roboto">
      <div v-if="showEditDR" class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 overflow-y-auto">
        <div @click="$emit('closeModal')"  class="absolute inset-0"></div>
  
        <div class="relative bg-white shadow-lg rounded-md w-full [max-width:95rem] mx-auto mt-10 mb-10">
          <!-- Header -->
          <div class="sticky top-0 dark:bg-custom-table bg-custom-blue p-5 rounded-t-md z-10 flex items-center justify-between">
            <h1 class="text-lg text-white font-semibold flex m-auto" >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
              </svg>
              <span v-if="userRole === 'manager'">EDIT STATUS OF DELIVERY RECEIPT #{{ item.dr_number }}</span>
               <span v-else>EDIT DELIVERY RECEIPT  #{{ item.dr_number }}</span>
            </h1>
            <button @click="$emit('closeModal')" class="text-white hover:text-gray-300 focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
  
          <!-- Editable Form -->
          <div class="p-4 space-y-8 dark:bg-custom-main">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 ">
              <!-- General Information -->
              <div class="bg-gray-100  dark:bg-custom-table p-6 rounded-md shadow-lg">
                <h2 class="font-medium text-gray-700 mb-6 dark:text-custom-white">General Information</h2>
                <div class="space-y-6">
                  <div>
                    <label class="text-sm text-gray-600  dark:text-custom-white ">Name:</label>
                    <input 
                      v-model="item.name" 
                      :disabled="userRole !== 'procurement'" 
                      class="text-xs p-2 border border-gray-300 rounded-md w-full dark:bg-custom-table dark:text-custom-white"
                      :class="userRole !== 'procurement' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'"
                    />
                  </div>
                  <div>
                    <label class="text-sm text-gray-600  dark:text-custom-white">Project Name:</label>
                    <input v-model="item.project_name" 
                    :disabled="userRole !== 'procurement'" 
                      class="text-xs p-2 border border-gray-300 rounded-md w-full dark:bg-custom-table dark:text-custom-white"
                      :class="userRole !== 'procurement' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'" />
                  </div>
                  <div>
                  <label for="status" class="text-sm text-gray-600  dark:text-custom-white">Status:</label>
                  <select 
                          id="status" 
                          v-model="item.status" 
                          placeholder="Enter unit (e.g., 24 meters, 10 kg)"  
                          :disabled="userRole !== 'manager'"
                           class="w-full p-2 text-xs border border-gray-200 rounded-md bg-gray-200  focus:ring-2 focus:ring-blue-300 dark:bg-custom-table"
                        >
                          <option value="pending">pending</option>
                          <option value="approved">approved</option>
                          <option value="rejected">rejected</option>
                        </select>
                </div>
                  <div>
                    <label class="text-sm text-gray-600  dark:text-custom-white">Remarks:</label>
                    <textarea v-model="item.remarks" 
                    :disabled="!(userRole === 'procurement' || userRole === 'manager')"
                      class="text-xs p-2 border border-gray-300 rounded-md w-full dark:bg-custom-table dark:text-custom-white"
                      :class="(userRole === 'procurement' || userRole === 'manager') 
                            ? 'bg-white text-black' 
                            : 'bg-gray-200 text-gray-500 cursor-not-allowed'">
                </textarea>
                  </div>
                </div>
              </div>
  
              <!-- Material Details -->
              <div class="bg-gray-100  dark:bg-custom-table p-6 rounded-md shadow-lg">
                <h2 class="font-medium text-gray-700 mb-6 dark:text-custom-white">Material Details</h2>
                <div v-if="selectedmaterials.length === 0" class="text-gray-500 text-sm dark:text-custom-white ">No materials available.</div>
                <div v-for="(material, index) in selectedmaterials" :key="index" class="flex space-x-6 mt-6">
                    <div class="flex-1 ">
                      <label class="text-sm text-gray-600 dark:text-custom-white">Material:</label>
                      <input
                      list="materialList" 
                      v-model="material.material_name"   
                      :disabled="userRole !== 'procurement'" 
                      class="text-xs p-2 border border-gray-300 rounded-md w-full dark:bg-custom-table dark:text-custom-white" 
                      :class="userRole !== 'procurement' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'"/>
                      <datalist id="materialList">
                         <option v-for="(material, index) in materialNames" :key="index" :value="material"></option>
                      </datalist>


                    </div>

                    <div class="flex-1">
                    <label :for="'measurement_' + index" class="text-sm text-gray-600 dark:text-custom-white">Measurement:</label>
                    <input :id="'measurement_' + index" v-model="material.measurement"  placeholder="Enter unit (e.g., 24 meters, 10 kg)"   
                    :disabled="userRole !== 'procurement'" 
                      class="text-xs p-2 border border-gray-300 rounded-md w-full dark:bg-custom-table dark:text-custom-white"
                      :class="userRole !== 'procurement' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'" />
                    </div>

                    <div class="flex-1">
                    <label :for="'unit' + index" class="text-sm text-gray-600 dark:text-custom-white">Unit:</label>
                        <select 
                          :id="'unit_' + index" 
                          v-model="material.unit" 
                          placeholder="Enter unit (e.g., 24 meters, 10 kg)"  
                          :disabled="userRole !== 'procurement'" 
                          class="text-xs p-2 border border-gray-300 rounded-md w-full dark:bg-custom-table dark:text-custom-white"
                          :class="userRole !== 'procurement' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'">
                            
                          <option value="" disabled>Select unit</option>
                          <option value="pcs">pcs</option>
                          <option value="kg">Kg</option>
                          <option value="g">g</option>
                          <option value="m">m</option>
                          <option value="cm">cm</option>
                          <option value="mm">mm</option>
                          <option value="l">L</option>
                          <option value="ml">ml</option>
                          <option value="ft">ft</option>
                          <option value="in">inch</option>
                        </select>
                  </div>
                    <div class="flex-1" >
                      <label class="text-sm text-gray-600 dark:text-custom-white">Quantity:</label>
                      <input v-model="material.material_quantity"  
                      :disabled="userRole !== 'procurement'" 
                      class="text-xs p-2 border border-gray-300 rounded-md w-full dark:bg-custom-table dark:text-custom-white"
                      :class="userRole !== 'procurement' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'">
                    </div>
                  </div>
                <div  class="flex mt-5"  v-if="userRole === 'procurement'">
                  <p @click.prevent="addMaterialInput" class="text-blue-500 text-xs cursor-pointer">Add material +</p>
                </div>
  
                <!-- Delete Button -->
                <div v-if="userRole === 'procurement' && selectedmaterials.length> 1" class="flex justify-center mt-4">
                  <button @click.prevent="deleteMaterialInput" class="text-red-500 text-xs hover:text-red-700">
                    Delete Last Material
                  </button>
                </div>
              </div>
  
              <!-- Location Details -->
              <div class="bg-gray-100  dark:bg-custom-table p-6 rounded-md shadow-lg">
                <h2 class="font-medium text-gray-700 mb-6 dark:text-custom-white">Location Details</h2>
                <div>
                  <label class="text-sm text-gray-600 dark:text-custom-white">Location:</label>
                  <input v-model="item.location"   
                  :disabled="userRole !== 'procurement'" 
                  class="text-xs p-2 border border-gray-300 rounded-md w-full  dark:bg-custom-table dark:text-custom-white"
                  :class="userRole !== 'procurement' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'" />
                </div>
                <div class="flex justify-between mt-6">
                  <div class="w-1/2 pr-3">
                    <label class="text-sm text-gray-600 dark:text-custom-white">Latitude:</label>
                    <input v-model="item.latitude" 
                    :disabled="userRole !== 'procurement'" 
                    class="text-xs p-2 border border-gray-300 rounded-md w-full  dark:bg-custom-table dark:text-custom-white"
                    :class="userRole !== 'procurement' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'" />
                  </div>
                  <div class="w-1/2 pl-3">
                    <label class="text-sm text-gray-600 dark:text-custom-white">Longitude:</label>
                    <input v-model="item.longitude" 
                    :disabled="userRole !== 'procurement'" 
                     class="text-xs p-2 border border-gray-300 rounded-md w-full  dark:bg-custom-table dark:text-custom-white"
                    :class="userRole !== 'procurement' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'"  />
                  </div>
                </div>
  
                <!-- Leaflet Map (Only show when lat/lng exist) -->
                <div v-if="item.latitude && item.longitude" id="map" class="h-64 w-full mt-4"></div>
                <div v-else>
                  <p class="text-sm text-gray-500">Loading location details...</p>
                </div>
              </div>
            </div>
  
            <!-- Buttons -->
            <div class="flex justify-center mt-8 space-x-4" v-if="showSaveButton">
              <button @click="updateDR" class="px-32 py-2 rounded-lg bg-custom-blue dark:bg-custom-table text-white" >
                Save changes
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
import axios from "axios";
import "leaflet/dist/leaflet.css";
import Swal from "sweetalert2";
import L from 'leaflet'; // Ensure you import Leaflet
import {mapGetters} from 'vuex';
  
  export default {
    props: {
      showEditDR: Boolean,
      item: {
        type: Object,
        default: () => ({}),
      },
      materials: {
        type: Array,
        default: () => [],
      }
    },
    computed:{
     ...mapGetters('auth', ['user', 'userRole']),
    },
    data() {
      return {
        selectedmaterials: [],
        showSaveButton: true,  //✅ Keep button visible until saved
        materialNames: [],
        
      }
    },
    mounted() {
      this.initMap();
      this.fetchMaterialName();
    },
    watch: {
    showEditDR(newVal) {
      if (newVal) {
        this.initMap();
      }
  },
      item: {
        handler(newItem) {
          console.log('Item updated:', newItem); // Debugging log

          if (newItem) {
            // Ensure `selectedmaterials` updates correctly
            this.selectedmaterials = newItem.materials ? [...newItem.materials] : [];

            // Ensure `status` updates correctly
            this.selectedStatus = newItem.status || ''; 

              // ✅ Track the original status (before user changes)
            this.originalStatus = newItem.status;

    
          }
        },
        deep: true,
        immediate: true,
      },
    },
    methods: {
      addMaterialInput() {
          this.selectedmaterials.push({ 
            id: Date.now(), // Temporary ID to track items
            material_name: '', 
            measurement:null, 
            unit: '', 
            material_quantity: null 
          });
        },
        deleteMaterialInput(index) {
        if (this.selectedmaterials.length > 0) {
            this.selectedmaterials.pop(index, 1); // Properly remove the material from the array
        }
    },
      // When you update materials in the form
      updateMaterials() {
        this.$emit('update-materials', this.selectedmaterials);  // Emit event to parent to update materials
        this.$emit('closeModal'); // Close the modal
      },
      async updateDR() {
    try {
        // Prepare the updated data, including both status and materials
        const updatedData = { 
            ...this.item, 
            status: this.item.status,  // Ensure the status is included
            materials: this.selectedmaterials , // Include selected materials
            remarks: this.item.remarks
        };

        // Log the updated data after it's initialized
        console.log("Before sending update:", updatedData);
        console.log("Selected materials:", this.selectedmaterials);

        // Send the PATCH request to update both status and materials in the DR
        const response = await axios.patch(`/api/Dr/${updatedData.id}`, updatedData);

        console.log("Updated DR Response:", response.data);

        // Emit events to update parent components with new data
        this.$emit("update-item", response.data.dr);  // Emit the updated DR object
        this.$emit("update-materials", this.selectedmaterials);  // Emit updated materials
        this.$emit("closeModal");  // Close the modal


        // Show success alert
        Swal.fire("Success!", "Delivery Receipt Updated Successfully!", "success");
    } catch (error) {
        console.error("Error updating DR:", error.response?.data || error.message);
        if (error.response?.status === 422 && error.response?.data?.error === "Mismatch") {
            Swal.fire("Error!", "Measurement does not match the existing inventory!", "warning");
        } else {
            Swal.fire("Error!", `Delivery Receipt Failed to Update: ${error.response?.data?.message || error.message}`, "error");
        }
    }
},
        initMap() {
          if (this.item.latitude && this.item.longitude) {
            this.map = L.map("map").setView([this.item.latitude, this.item.longitude], 15);
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(this.map);
            this.marker = L.marker([this.item.latitude, this.item.longitude]).addTo(this.map);
          }
        },
        updateMapMarker() {
          if (this.map && this.marker) {
            this.marker.setLatLng([this.item.latitude, this.item.longitude]);
            this.map.setView([this.item.latitude, this.item.longitude]);
          }
       },
       async fetchMaterialName(){
       try{
        const response = await axios.get('/api/materials_name');
        console.log("Fetch material name successfull", response.data);
        this.materialNames = response.data;  // Assuming the API returns an array

       } catch (error){
            console.error("Error!", "Fetching materials name", error);
       }
      
    }
         
      
    },
  };
  </script>