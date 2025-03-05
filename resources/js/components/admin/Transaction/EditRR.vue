<template>
    <div class="font-roboto">
      <div v-if="showEditReturn" class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 overflow-y-auto">
        <div @click="$emit('closeEditReturn')"  class="absolute inset-0"></div>
  
        <div class="relative bg-white shadow-lg rounded-md w-full [max-width:95rem] mx-auto mt-10 mb-10">
          <!-- Header -->
          <div class="sticky top-0 bg-custom-blue p-4 rounded-t-md z-10 flex items-center justify-between">
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
          <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
              <!-- General Information -->
              <div class="bg-gray-100 p-6 rounded-md shadow-lg">
                <h2 class="font-medium text-gray-700 mb-6">General Information</h2>
                <div class="space-y-6">
                  <div>
                    <label class="text-sm text-gray-600">Name:</label>
                    <input 
                      v-model="item.name" 
                      :disabled="!(userRole === 'warehouse_staff' || userRole === 'manager')" 
                      class="text-xs p-2 border border-gray-300 rounded-md w-full"
                      :class="!(userRole === 'warehouse_staff' || userRole === 'manager') ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'"
                    />
                  </div>
                  <div>
                    <label class="text-sm text-gray-600">Project Name:</label>
                    <input v-model="item.project_name" 
                    :disabled="userRole !== 'warehouse_staff'" 
                      class="text-xs p-2 border border-gray-300 rounded-md w-full"
                      :class="userRole !== 'warehouse_staff' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'" />
                  </div>
                  <div>
                  <label for="status" class="text-sm text-gray-600">Status:</label>
                  <select 
                          id="status" 
                          v-model="item.status" 
                          placeholder="Enter unit (e.g., 24 meters, 10 kg)"  
                          :disabled="!(userRole  === 'manager' || item.status  === 'approved' ||  item.status ==='rejected')" 
                           class="w-full p-2 text-xs border border-gray-200 rounded-md bg-gray-200  focus:ring-2 focus:ring-blue-300"
                        >
                          <option value="pending">pending</option>
                          <option value="approved">approved</option>
                          <option value="rejected">rejected</option>
                        </select>
                </div>
  
                <div class="w-full bg-white rounded-md p-3 ">
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
                      class="text-xs p-2 border border-gray-300 rounded-md w-full"
                      :class="userRole !== 'warehouse_staff' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'" >
                </textarea>
                  </div>
                </div>
              </div>
  
              <!-- Material Details -->
              <div class="bg-gray-100 p-6 rounded-md shadow-lg">
                <h2 class="font-medium text-gray-700 mb-6">Material Details</h2>
                <div v-if="selectedReturnMaterials.length === 0" class="text-gray-500 text-sm">No materials available.</div>
                <div v-for="(material, index) in selectedReturnMaterials" :key="index" class="flex space-x-6 mt-6">
                    <div class="flex-1 ">
                      <label class="text-sm text-gray-600">Material:</label>
                      <input v-model="material.material_name"   
                      class="text-xs p-2 border border-gray-300 rounded-md w-full"/>
                    </div>
  
                    <div class="flex-1">
                    <label :for="'measurement_' + index" class="text-sm text-gray-600">Measurement:</label>
                    <input :id="'measurement_' + index" v-model="material.measurement"  placeholder="Enter unit (e.g., 24 meters, 10 kg)"   
                      class="text-xs p-2 border border-gray-300 rounded-md w-full" />
                    </div>
  
                    <div class="flex-1">
                    <label :for="'unit' + index" class="text-sm text-gray-600">Unit:</label>
                        <select 
                          :id="'unit_' + index" 
                          v-model="material.unit" 
                          placeholder="Enter unit (e.g., 24 meters, 10 kg)"  
                          class="text-xs p-2 border border-gray-300 rounded-md w-full">
                            
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
                      <label class="text-sm text-gray-600">Quantity:</label>
                      <input v-model="material.material_quantity"  
                      class="text-xs p-2 border border-gray-300 rounded-md w-full">
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
              <div class="bg-gray-100 p-6 rounded-md shadow-lg">
                <h2 class="font-medium text-gray-700 mb-6">Location Details</h2>
                <div>
                  <label class="text-sm text-gray-600">Location:</label>
                  <input v-model="item.location"   
                  :disabled="!(userRole === 'warehouse_staff' || userRole === 'manager')" 
                  class="text-xs p-2 border border-gray-300 rounded-md w-full"
                  :class="userRole !== 'warehouse_staff' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'" />
                </div>
                <div class="flex justify-between mt-6">
                  <div class="w-1/2 pr-3">
                    <label class="text-sm text-gray-600">Latitude:</label>
                    <input v-model="item.latitude" 
                    :disabled="userRole !== 'procurement'" 
                    class="text-xs p-2 border border-gray-300 rounded-md w-full"
                    :class="userRole !== 'warehouse_staff' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'" />
                  </div>
                  <div class="w-1/2 pl-3">
                    <label class="text-sm text-gray-600">Longitude:</label>
                    <input v-model="item.longitude" 
                    :disabled="userRole !== 'procurement'" 
                     class="text-xs p-2 border border-gray-300 rounded-md w-full"
                    :class="userRole !== 'warehouse_staff' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'"  />
                  </div>
                </div>
  
                <!-- Leaflet Map (Only show when lat/lng exist) -->
                <div v-if="item.latitude && item.longitude"  v-show="!isFullScreen"  id="map"  :key="mapKey"  class="h-64 w-full mt-4"></div>
                <div v-else>
                  <p class="text-sm text-gray-500">Loading location details...</p>
                </div>
              </div>
            </div>
  
            <!-- Buttons -->
            <div class="flex justify-center mt-8 space-x-4" >
              <button @click="updateEditReturn" class="px-32 py-2 rounded-lg bg-custom-blue text-white">
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
             map: null                      // Store Leaflet map instance
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
 
                     if (newItem.latitude && newItem.longitude) {
                         this.updateMapMarker();
                     }
                 }
             },
             deep: true,
             immediate: true
         },
         showEditReturn(newVal) {
             if (newVal) {
                 this.initMap();
             }
         }
     },
     computed: {
         ...mapGetters("auth", ["user", "userRole"]),
     },
     mounted() {
         this.initMap();
     },
     methods: {
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
                 this.initMap();
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
            }

     }
 };
 </script>