<template>
    <div class="font-roboto">
      <!-- Button to Open Modal -->
      <button @click="showDrmodal = true" class=" px-4 py-2 rounded-lg flex items-center justify-center font-semibold bg-custom-blue text-white drop-shadow-md whitespace-nowrap">
        <span>Add DR</span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 relative left-1">
          <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 9a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25V15a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25V9Z" clip-rule="evenodd" />
        </svg>
      </button>
  
      <div v-if="showDrmodal" class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 overflow-y-auto">
        <!-- Modal Overlay (Click to Close) -->
        <div @click="showDrmodal = false" class="absolute inset-0"></div>
  
        <!-- Modal Content -->
        <div class="relative bg-white shadow-lg rounded-md w-full max-w-7xl mx-auto mt-10 mb-10">
          <!-- Header -->
          <div class="sticky top-0 bg-custom-blue p-4 rounded-t-md z-10">
            <div class="flex items-center justify-between">
              <h1 class="text-lg text-white font-semibold flex m-auto">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
            CREATE DELIVERY RECEIPT
          </h1>
              <button @click="showDrmodal = false" class="text-white hover:text-gray-300 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
  
          <!-- Form Content -->
          <form @submit.prevent="addMaterialDatabase" class="p-8 space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
              <!-- General Information Column -->
              <div class="bg-gray-100 p-6 rounded-md shadow-lg">
                <h2 class="font-medium text-gray-700 mb-6">General Information</h2>
                <div class="space-y-6">
                  <div>
                    <label for="name" class="text-sm text-gray-600">Name:</label>
                    <input id="name" v-model="formData.name" type="text" class="w-full p-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300">
                  </div>
                  <div>
                    <label for="project_name" class="text-sm text-gray-600">Project Name:</label>
                    <input id="project_name" v-model="formData.project_name" type="text" class="w-full p-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300">
                  </div>
                  <div>
                    <label for="status" class="text-sm text-gray-600">Status:</label>
                    <input id="status" v-model="formData.status" type="text" readonly class="w-full p-2 text-xs border border-gray-300 rounded-md bg-gray-100 text-gray-600 focus:ring-2 focus:ring-blue-300">
                  </div>
                  <div>
                    <label for="remarks" class="text-sm text-gray-600">Remarks:</label>
                    <textarea id="remarks" v-model="formData.remarks" class="w-full p-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300" rows="4" ></textarea>
                  </div>
                </div>
              </div>
  
              <!-- Material Details Column -->
              <div class="bg-gray-100 p-6 rounded-md shadow-lg">
                <h2 class="font-medium text-gray-700 mb-6">Material Details</h2>
                <div class="space-y-6">
                  <!-- Iterate over materials dynamically -->
                  <div v-for="(material, index) in formData.materials" :key="index" class="flex space-x-6">
                    <div class="flex-1">
                      <label :for="'material_' + index" class="text-sm text-gray-600">Material:</label>
                      <input :id="'material_' + index" v-model="material.material_name" type="text" class="w-full p-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300">
                    </div>
                    <div class="flex-1">
                      <label :for="'measurement_' + index" class="text-sm text-gray-600">Measurement:</label>
                      <input :id="'measurement_' + index" v-model="material.measurement_unit"  placeholder="Enter unit (e.g., 24 meters, 10 kg)" class="w-full p-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300">
                    </div>
                    <div class="flex-1">
                      <label :for="'quantity_' + index" class="text-sm text-gray-600">Quantity:</label>
                      <input :id="'quantity_' + index" v-model="material.material_quantity" type="number" class="w-full p-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300">
                    </div>
                  </div>
  
                  <div class="flex mt-5">
                    <p @click.prevent="addMaterialInput" class="text-blue-500 text-xs cursor-pointer">Add material +</p>
                  </div>
  
                  <!-- Delete Button -->
                  <div v-if="formData.materials.length > 1" class="flex justify-center mt-4">
                    <button @click.prevent="deleteMaterialInput" class="text-red-500 text-xs hover:text-red-700">
                      Delete Last Material
                    </button>
                  </div>
                </div>
              </div>
  
              <!-- Location Details Column -->
              <div class="bg-gray-100 p-6 rounded-md shadow-lg">
                <h2 class="font-medium text-gray-700 mb-6">Location Details</h2>
                <div class="space-y-6">
                  <div>
                    <label for="location" class="text-sm text-gray-600">Location:</label>
                    <input id="location" v-model="formData.location" type="text" class="w-full p-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300">
                  </div>
                  <div class="flex justify-between">
                    <div class="w-1/2 pr-3">
                      <label for="latitude" class="text-sm text-gray-600">Latitude:</label>
                      <input id="latitude" v-model="formData.latitude" type="number" step="any" class="w-full p-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300">
                    </div>
                    <div class="w-1/2 pl-3">
                      <label for="longitude" class="text-sm text-gray-600">Longitude:</label>
                      <input id="longitude" v-model="formData.longitude" type="number" step="any" class="w-full p-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300">
                    </div>
                  </div>
                  <div v-if="showMap && formData.latitude && formData.longitude">
                    <div id="map" style="height: 400px; width: 100%;"></div>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Submit Button -->
            <div class="flex justify-center mt-8">
              <button type="submit" class="px-32 py-2 rounded-lg bg-custom-blue text-white ">
                Submit
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script>
import axios from 'axios';
import Swal from 'sweetalert2';
import { nextTick } from "vue";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
  
  export default {
    data() {
      return {
        showDrmodal: false,
        map: null,
        marker: null,
        showMap: true, // Control to show map
        formData: {
          name: '',
          project_name: '',
          status: 'pending',
          remarks: null,
          materials: [{ material_name: '', measurement_unit: '', material_quantity: null }],
          location: '',
          latitude: '',
          longitude: '',
        },
      };
    },
    methods: {
      async addMaterialDatabase() {
        if (
          !this.formData.name ||
          !this.formData.project_name ||
          !this.formData.status ||
          !this.formData.location ||
          !this.formData.latitude ||
          !this.formData.longitude ||
          this.formData.materials.some(material => 
            !material.material_name?.trim() ||  // Ensure material name is not empty
            !material.measurement_unit?.trim() || 
             material.material_quantity === null
          )
        ) {
          Swal.fire({
            title: 'Warning!',
                text: 'Please fill in all the required fields before submitting.',
                icon: 'warning',
                confirmButtonText: 'OK'
          });
          return;
        }
  
        try {
          const response = await axios.post('/api/Dr', this.formData);
          if (response.data.success) {
            this.$emit("closeModal");
            this.showDrmodal = false;
            Swal.fire({
              icon: 'success',
              title: 'Successfully added!',
              showConfirmButton: true,
            }).then(() => {
              this.resetForm();
              this.$emit('addedDR', response.data.dR);

            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Failed to add the DR.',
              showConfirmButton: true,
            });
          }
        } catch (error) {
          Swal.fire({
            icon: 'error',
            title: 'An error occurred.',
            showConfirmButton: true,
          });
        }
      },
  
      addMaterialInput() {
        this.formData.materials.push({ material_name: '', measurement_unit: '', material_quantity: null });
      },
  
      deleteMaterialInput() {
        if (this.formData.materials.length > 1) {
          this.formData.materials.pop();
        }
      },
  
      resetForm() {
        this.formData = {
          name: '',
          project_name: '',
          status: 'pending',
          remarks: null,
          materials: [{ material_name: '', measurement_unit: '', material_quantity: null }],
          location: '',
          latitude: '',
          longitude: '',
        };
        this.showMap = false;
      },
  
      initLeafletMap() {
          nextTick(() => {
            if (this.map) {
              this.map.remove();
            }

            if (this.formData.latitude && this.formData.longitude) {
              this.map = L.map("map").setView([this.formData.latitude, this.formData.longitude], 15);
              L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                attribution: "© OpenStreetMap contributors",
              }).addTo(this.map);
              this.marker = L.marker([this.formData.latitude, this.formData.longitude]).addTo(this.map);
            }
          });
        }
    },
  
    watch: {
      'formData.latitude'(newVal) {
        if (newVal && this.formData.longitude) {
          this.initLeafletMap();
        }
      },
      'formData.longitude'(newVal) {
        if (newVal && this.formData.latitude) {
          this.initLeafletMap();
        }
      },
    },
  };
  </script>
  <style>
  #map {
    height: 400px;
    width: 100%;
  }
  </style>