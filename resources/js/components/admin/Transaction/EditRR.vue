<template>
    <div class="font-roboto">
      <div v-if="showEditReturn" class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 overflow-y-auto">
        <div @click="$emit('closeEditReturn')"  class="absolute inset-0"></div>
  
        <div class="relative bg-white shadow-lg rounded-md w-full [max-width:95rem] mx-auto mt-10 mb-10">
          <!-- Header -->
          <div class="sticky top-0 dark:bg-custom-table bg-custom-blue p-4 rounded-t-md z-10 flex items-center justify-between">
            <h1 class="text-lg text-white font-semibold flex m-auto" >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
              </svg>
              <span v-if="(userRole === 'warehouse_staff' || userRole === 'manager')">
                EDIT RETURN RECEIPT #{{ item.rr_number }}
              </span>
  
            </h1>
            <button @click="$emit('closeEditReturn')" class="text-white hover:text-gray-300 focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
  
          <!-- Editable Form -->
          <div class="p-8 space-y-8 dark:bg-custom-main">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
              <!-- General Information -->
              <div class="bg-gray-100 dark:bg-custom-table p-6 rounded-md shadow-lg">
                <h2 class="font-medium text-gray-700 mb-6 dark:text-custom-white">General Information</h2>
                <div class="space-y-6">
                  <div>
                    <label class="text-sm text-gray-600  dark:text-custom-white ">Name:</label>
                    <input 
                      v-model="item.name" 
                      :disabled="!(userRole === 'warehouse_staff' || userRole === 'manager')" 
                      class="text-xs p-2 border border-gray-300 rounded-md w-full  dark:bg-custom-table dark:text-custom-white"
                      :class="!(userRole === 'warehouse_staff' || userRole === 'manager') ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'"
                    />
                  </div>
                  <div>
                    <label class="text-sm text-gray-600 le dark:text-custom-white">Project Name:</label>
                    <input v-model="item.project_name" 
                    :disabled="userRole !== 'warehouse_staff'" 
                      class="text-xs p-2 border border-gray-300 rounded-md w-full  dark:bg-custom-table dark:text-custom-white"
                      :class="userRole !== 'warehouse_staff' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'" />
                  </div>
                  <div>
                  <label for="status" class="text-sm text-gray-600  dark:text-custom-white">Status:</label>
                  <select 
                          id="status" 
                          v-model="item.status" 
                          placeholder="Enter unit (e.g., 24 meters, 10 kg)"  
                          :disabled="userRole !== 'manager'"
                           class="w-full p-2 text-xs border bg-gray-300 border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300  dark:bg-custom-table dark:text-custom-white"
                        >
                          <option value="pending">pending</option>
                          <option value="approved">approved</option>
                          <option value="rejected">rejected</option>
                        </select>
                </div>
  
                <div class="text-xs p-2 border bg-white rounded-md cursor-pointer text-blue-600 underline dark:bg-custom-table">
                        <!-- Hidden File Input -->
                        <input type="file" ref="fileInput" @change="handleFileUpload" accept="image/*" class="hidden" />
                        
                        <!-- Upload Button -->
                        <button class="text-xs text-blue-500 underline" @click="triggerFileInput">Upload File</button>

                        <!-- Show File Name Instead of Image -->
                        <div v-if="fileName || item.return_proof" class="mt-2">
                            📄 
                            <span 
                                class="text-blue-500 underline cursor-pointer text-md" 
                                @click="openFullScreen"
                            >
                                {{ fileName || item.return_proof_original_name }}
                            </span>
                            <button @click="removeFile" class="text-red-500 text-xs ml-2">✖</button>
                        </div>

                        <!-- Fullscreen Modal (For Viewing Image) -->
                        <div 
                            v-if="isFullScreen" 
                            class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
                        >
                            <img :src="imagePreview" class="max-w-full max-h-full" />
                            <button 
                                @click="closeFullScreen" 
                                class="absolute top-5 right-5 text-white text-2xl bg-black p-2 rounded-full"
                            >
                                ✖
                            </button>
                        </div>
                    </div>
  
                <div>
                    <label class="text-sm text-gray-600">Return remarks:</label>
                    <textarea v-model="item.remarks" 
                    :disabled="userRole !== 'warehouse_staff'" 
                      class="text-xs p-2 border border-gray-300 rounded-md w-full dark:bg-custom-table dark:text-custom-white"
                      :class="userRole !== 'warehouse_staff' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'" >
                </textarea>
                  </div>
                </div>
              </div>
  
              <!-- Material Details -->
              <div class="bg-gray-100 dark:bg-custom-table p-6 rounded-md shadow-lg">
                <h2 class="font-medium text-gray-700 mb-6 dark:text-custom-white">Material Details</h2>
                <div v-if="selectedReturnMaterials.length === 0" class="text-gray-500 text-sm dark:text-custom-white">No materials available.</div>
                <div v-for="(material, index) in selectedReturnMaterials" :key="index" class="flex space-x-6 mt-6">
                    <div class="flex-1 ">
                      <label class="text-sm text-gray-600 dark:text-custom-white">Material:</label>
                      <input list="materialList" v-model="material.material_name"   
                      class="text-xs p-2 border border-gray-300 rounded-md w-full dark:bg-custom-table dark:text-custom-white"/>
                      <datalist id="materialList">
                          <option v-for="(material, index) in materialName" :key="index" :value="material"></option>
                      </datalist>
                    </div>
  
                    <div class="flex-1">
                    <label :for="'measurement_' + index" class="text-sm text-gray-600  dark:text-custom-white">Measurement:</label>
                    <input :id="'measurement_' + index" v-model="material.measurement"  placeholder="Enter unit (e.g., 24 meters, 10 kg)"   
                      class="text-xs p-2 border border-gray-300 rounded-md w-full dark:bg-custom-table dark:text-custom-white" />
                    </div>
  
                    <div class="flex-1">
                    <label :for="'unit' + index" class="text-sm text-gray-600dark:text-custom-white">Unit:</label>
                        <select 
                          :id="'unit_' + index" 
                          v-model="material.unit" 
                          placeholder="Enter unit (e.g., 24 meters, 10 kg)"  
                          class="text-xs p-2 border border-gray-300 rounded-md w-full dark:bg-custom-table dark:text-custom-white">
                            
                          <option value="" disabled>Select unit</option>
                          <option value="pcs">Pcs</option>
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
                      <label class="text-sm text-gray-600  dark:text-custom-white">Quantity:</label>
                      <input v-model="material.material_quantity"  
                      class="text-xs p-2 border border-gray-300 rounded-md w-full dark:bg-custom-table dark:text-custom-white">
                    </div>
                  </div>
                <div  class="flex mt-5"  v-if="userRole === 'warehouse_staff'">
                  <p @click.prevent="addMaterialInput" class="text-blue-500 text-xs cursor-pointer">Add material +</p>
                </div>
  
                <!-- Delete Button -->
                <div v-if="userRole === 'warehouse_staff' && selectedReturnMaterials.length> 1" class="flex justify-center mt-4">
                  <button @click.prevent="deleteMaterialInput" class="text-red-500 text-xs hover:text-red-700">
                    Delete Last Material
                  </button>
                </div>
              </div>
  
              <!-- Location Details -->
              <div class="bg-gray-100 dark:bg-custom-table p-6 rounded-md shadow-lg">
                <h2 class="font-medium text-gray-700 mb-6  dark:text-custom-white">Location Details</h2>
                <div>
                  <label class="text-sm text-gray-600  dark:text-custom-white">Location:</label>
                  <input v-model="item.location"  
                   readonly 
                  :disabled="!(userRole === 'warehouse_staff' || userRole === 'manager')" 
                  class="text-xs p-2 border border-gray-300 bg-gray-300 rounded-md w-full dark:bg-custom-table dark:text-custom-white  text-gray-500 "
                  :class="userRole !== 'warehouse_staff' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'" />
                </div>

                <!-- Leaflet Map (Only show when lat/lng exist) -->
                <div v-if="showMap"  v-show="!isFullScreen"  id="map"  :key="mapKey" style="height: 400px; width: 100%;"  class="h-64 w-full mt-4"></div>
              </div>
            </div>
  
            <!-- Buttons -->
            <div class="flex justify-center mt-8 space-x-4" >
              <button  @click="updateEditReturn" class="px-32 py-2 rounded-lg bg-custom-blue text-white dark:bg-custom-table">
                Save Return Receipt
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
 <script>
 import { mapGetters } from 'vuex';
 import L from 'leaflet';
 import "leaflet/dist/leaflet.css";
 import Swal from 'sweetalert2';
 import axios from 'axios';
 import "leaflet-control-geocoder/dist/Control.Geocoder.css";
import "leaflet-control-geocoder";

 
 export default {
     props: {
         showEditReturn: Boolean,
         item: {
             type: Object,
             default: () => ({}),
         },
         materials: {
             type: Object,
             default: () => [],
         }
     },
     data() {
         return {
             selectedReturnMaterials: [], 
             file: null,                  // Store selected file
             fileName: "",                 // Display file name
             previewUrl: null,             // Store preview URL (for external images)
             imagePreview: null,           // Image preview for fullscreen
             isFullScreen: false,          // Controls full screen view
             mapKey: 0,                     // Key to force map refresh
             map: null,       
             marker:null,
             latitude: null,    // 👈 ADD THIS
             longitude:null,    // 👈 AND THIS      // Store Leaflet map instance
             materialName: [],
             showMap: true,

         };
     },
     watch: {
         item: {
             handler(newItem) {
                 console.log('New item', newItem);
                 if (newItem) {
                     this.selectedReturnMaterials = this.materials && Array.isArray(this.materials) ? [...this.materials] : [];
 
                      // Set file preview and file name if existing proof exists
                      if (newItem.returnproof) {
                            this.previewUrl = `/storage/${newItem.returnproof}`;  // Correct relative path
                            this.fileName = newItem.return_proof_original_name || this.extractFileName(newItem.returnproof);
                            this.imagePreview = `/storage/${newItem.returnproof}`;  // Correct path for image preview
                        }

        
                     
                 }
             },
             deep: true,
             immediate: true
         },
         showEditReturn(newVal) {
             if (newVal) {
                 this.initLeafletMap();
             }
         }
     },
     computed: {
         ...mapGetters("auth", ["user", "userRole"]),
     },
     mounted() {
         this.initLeafletMap();
         this.fetchMaterialName();
     },
     methods: {
      async initLeafletMap() {
        if (!this.item.location) {
          console.log("No location provided");
          return;  // Exit early if no location
        }

        // Use OpenCage Geocoding API to get the coordinates from the address
        const address = this.item.location;
        const apiKey = 'aabf9bb2902248e99c7f4e2709bd7cff'; // Replace with your actual API key
        const url = `https://api.opencagedata.com/geocode/v1/json?key=${apiKey}&q=${encodeURIComponent(address)}&limit=1`;

        try {
          const response = await fetch(url);
          const data = await response.json();

          if (data.results && data.results.length) {
            // Extract the latitude and longitude from the API response
            const location = data.results[0].geometry;
            this.latitude = location.lat;
            this.longitude = location.lng;

            this.$nextTick(() => {
              const mapContainer = document.getElementById("map");

              if (mapContainer) {
                if (this.map) {
                  this.map.remove(); // Destroy existing map if it exists
                }

                // Initialize the map with the retrieved latitude and longitude
                this.map = L.map(mapContainer).setView([this.latitude, this.longitude], 20); // Default zoom level

                // Add OpenStreetMap tile layer (base map)
                L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                  attribution: "© OpenStreetMap contributors",
                }).addTo(this.map);

                // Add a marker at the location (if exists)
                this.marker = L.marker([this.latitude, this.longitude]).addTo(this.map);
              }
            });
          } else {
            console.error("Location not found for the address");
          }
        } catch (error) {
          console.error("Failed to fetch location coordinates:", error);
        }
      },

         addMaterialInput() {
             this.selectedReturnMaterials.push({ 
                 id: Date.now(),
                 material_name: '', 
                 measurement: null, 
                 unit: '', 
                 material_quantity: null 
             });
         },
         deleteMaterialInput(index) {
             if (this.selectedReturnMaterials.length > 0) {
                 this.selectedReturnMaterials.pop(index, 1);
             }
         },
         async updateEditReturn() {
                try {
                    const formData = new FormData();

                    // Add dynamic fields (less repetitive)
                    const fields = {
                        id: this.item.id,
                        name: this.item.name,
                        project_name: this.item.project_name,
                        status: this.item.status,
                        remarks: this.item.remarks || '',
                        location: this.item.location,
                        latitude: this.item.latitude,
                        longitude: this.item.longitude,
                    };

                    Object.entries(fields).forEach(([key, value]) => {
                        formData.append(key, value);
                    });

                    // Append materials
                    formData.append('materials', JSON.stringify(this.selectedReturnMaterials));

                    // Append file if a new file is selected
                    if (this.file) {
                        formData.append('return_proof', this.file);
                    }

                    console.log('Before updating data', [...formData.entries()]);  // Debug log to see all form data

                    const response = await axios.post(`/api/updateReturnReceipt/${this.item.id}`, formData, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    });

                    console.log('After updating data', response.data);

                    this.$emit('update-ReturnReceipt-item', response.data.rr);
                    this.$emit('update-ReturnReceipt-materials', this.selectedReturnMaterials);
                    this.$emit('closeEditReturn');

                    Swal.fire("Success!", "Return Receipt Updated Successfully!", "success");
                } catch (error) {
                    console.error("Error updating RR", error.response?.data || error.message);
                    Swal.fire("Error!", `Return Receipt failed to Update: ${error.response?.data?.message || error.message}`, "error");
                }
            },


         extractFileName(url) {
             // Example: Extract file name from URL if file comes from backend
             return url.split('/').pop();
         },
         triggerFileInput() {
             this.$refs.fileInput.click();
         },
         handleFileUpload(event) {
             const file = event.target.files[0];
             if (file) {
                 this.file = file;
                 this.fileName = file.name;
                 this.imagePreview = URL.createObjectURL(file);
             }
         },
         openFullScreen() {
                if (this.file) {
                    // For newly uploaded file (during the current edit session)
                    this.imagePreview = URL.createObjectURL(this.file);
                } else if (this.item.return_proof) {
                    this.imagePreview = this.getStorageUrl(this.item.return_proof);
                }

                console.log('Opening Fullscreen with Image URL:', this.imagePreview); // Debug
                this.isFullScreen = true;
            },
        closeFullScreen() {
             this.isFullScreen = false;
             this.mapKey++;
             setTimeout(() => {
                 this.initLeafletMap();
             }, 100);
         },
         removeFile() {
                if (this.file && this.imagePreview.startsWith('blob:')) {
                    URL.revokeObjectURL(this.imagePreview);
                }
                this.file = null;
                this.fileName = this.item.return_proof_original_name || "";

                this.imagePreview = ''; // Force refresh trick
                this.$nextTick(() => {
                    this.imagePreview = this.previewUrl; // Restore original image preview
                });
            },
            getStorageUrl(filePath) {
                if (!filePath) return '';
                // Handle absolute URL (for cases where backend already provides full URL)
                if (filePath.startsWith('http')) return filePath;
                // Handle relative path from database (like return_proofs/...)
                return `/storage/${filePath}`;
            },
            async fetchMaterialName(){
              try{
                const response = await axios.get('/api/materials_name');
              console.log("Fetch Material names success", console.log);
              this.materialName = response.data;
              } catch(error){
                console.error("Error fetching material names", error);
              }
            }
     }
 };
 </script>