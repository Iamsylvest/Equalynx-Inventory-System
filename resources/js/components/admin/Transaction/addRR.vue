<template>
  <div class="font-roboto">
    <div v-if="showEditDR_Return" class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 overflow-y-auto">
      <div @click="$emit('closeModalReturnModal')"  class="absolute inset-0"></div>

      <div class="relative bg-white shadow-lg rounded-md w-full [max-width:95rem] mx-auto mt-10 mb-10">
        <!-- Header -->
        <div class="sticky top-0 bg-custom-blue p-4 rounded-t-md z-10 flex items-center justify-between">
          <h1 class="text-lg text-white font-semibold flex m-auto" >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            <span v-if="userRole === 'warehouse_staff'">
              CREATE RETURN RECEIPT OF DELIVERY RECEIPT #{{ item.dr_number }}
            </span>

          </h1>
          <button @click="$emit('closeModalReturnModal')" class="text-white hover:text-gray-300 focus:outline-none">
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
                    :disabled="userRole !== 'procurement'" 
                    class="text-xs p-2 border border-gray-300 rounded-md w-full"
                    :class="userRole !== 'procurement' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'"
                  />
                </div>
                <div>
                  <label class="text-sm text-gray-600">Project Name:</label>
                  <input v-model="item.project_name" 
                  :disabled="userRole !== 'procurement'" 
                    class="text-xs p-2 border border-gray-300 rounded-md w-full"
                    :class="userRole !== 'procurement' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'" />
                </div>
                <div>
                <label for="status" class="text-sm text-gray-600">Status:</label>
                <select 
                        id="status" 
                        v-model="item.status" 
                        placeholder="Enter unit (e.g., 24 meters, 10 kg)"  
                        :disabled="userRole  !== 'manager' || item.status  === 'approved' ||  item.status ==='rejected'" 
                         class="w-full p-2 text-xs border border-gray-200 rounded-md bg-gray-200  focus:ring-2 focus:ring-blue-300"
                      >
                        <option value="pending">pending</option>
                        <option value="approved">approved</option>
                        <option value="rejected">rejected</option>
                      </select>
              </div>

              <div class="w-full bg-white rounded-md p-3">
                    <!-- Hidden File Input -->
                    <input type="file" ref="fileInput" @change="handleFileUpload" accept="image/*" class="hidden" />
                    
                    <!-- Upload Button -->
                    <button class="text-xs text-blue-500 underline" @click="triggerFileInput">Upload File</button>

                    <!-- Show File Name Instead of Image -->
                    <div v-if="fileName" class="mt-2">
                      📄 <span class="text-blue-500 underline cursor-pointer" @click="openFullScreen">{{ fileName }}</span>
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
              <div v-if="selectedmaterials.length === 0" class="text-gray-500 text-sm">No materials available.</div>
              <div v-for="(material, index) in selectedmaterials" :key="index" class="flex space-x-6 mt-6">
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
              <div v-if="userRole === 'warehouse_staff' && selectedmaterials.length> 1" class="flex justify-center mt-4">
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
                :disabled="userRole !== 'procurement'" 
                class="text-xs p-2 border border-gray-300 rounded-md w-full"
                :class="userRole !== 'procurement' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'" />
              </div>
              <div class="flex justify-between mt-6">
                <div class="w-1/2 pr-3">
                  <label class="text-sm text-gray-600">Latitude:</label>
                  <input v-model="item.latitude" 
                  :disabled="userRole !== 'procurement'" 
                  class="text-xs p-2 border border-gray-300 rounded-md w-full"
                  :class="userRole !== 'procurement' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'" />
                </div>
                <div class="w-1/2 pl-3">
                  <label class="text-sm text-gray-600">Longitude:</label>
                  <input v-model="item.longitude" 
                  :disabled="userRole !== 'procurement'" 
                   class="text-xs p-2 border border-gray-300 rounded-md w-full"
                  :class="userRole !== 'procurement' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'"  />
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
            <button @click="updateDR" class="px-32 py-2 rounded-lg bg-custom-blue text-white">
              Save Return Receipt
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
import L from "leaflet";
import { mapGetters } from "vuex";

export default {
props: {
  showEditDR_Return: Boolean,
  item: {
    type: Object,
    default: () => ({}),
  },
  materials: {
    type: Array,
    default: () => [],
  },
},
computed: {
  ...mapGetters("auth", ["user", "userRole"]),
},
data() {
  return {
    formData: JSON.parse(JSON.stringify(this.item)), // Deep copy
    selectedmaterials: [...this.materials], // Copy to prevent modifying props
    file: null, // Store selected file
    fileName: "", // Display file name
    previewUrl: null, // Store preview URL
    imagePreview: null, // Image preview for fullscreen
    isFullScreen: false, // Controls full screen view
    mapKey: 0, // Key to force map refresh
    map: null, // Store Leaflet map instance
    
  };
},
watch: {
  showEditDR_Return(newVal) {
    if (newVal) {
      this.initMap(); // Initialize map only when modal opens
    }
  },
  item: {
    handler(newItem) {
      console.log("Updating formData from item:", newItem);
      this.formData = JSON.parse(JSON.stringify(newItem)); // Deep copy
    },
    deep: true,
    immediate: true,
  },
},
mounted() {
  if (this.showEditDR_Return) {
    this.initMap();
  }
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
  initMap() {
    if (this.map) {
      this.map.remove(); // Remove old map instance
    }
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
  triggerFileInput() {
    this.$refs.fileInput.click(); // Simulate file input click
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
    if (this.imagePreview) {
      this.isFullScreen = true;
    }
  },
  closeFullScreen() {
    this.isFullScreen = false;
    this.mapKey++; // Change key to force map refresh
    setTimeout(() => {
      this.initMap(); // Ensure map is reinitialized
    }, 100);
  },
  removeFile() {
    if (this.imagePreview) {
      URL.revokeObjectURL(this.imagePreview);
    }
    this.file = null;
    this.fileName = "";
    this.imagePreview = null;
  },
  updateDR() {
    console.log("Before Sending: ", this.formData);
    if (!this.formData || !this.formData.id) {
      Swal.fire("Error", "Invalid Delivery Return Data!", "error");
      return;
    }

    let formData = new FormData();
    formData.append("dr_id", this.formData.id);
    this.selectedmaterials.forEach((material, index) => {
      Object.keys(material).forEach((key) => {
        formData.append(`materials[${index}][${key}]`, material[key]);
      });
    });
    formData.append("name", this.formData.name || "");
    formData.append("project_name", this.formData.project_name || "");
    formData.append(
      "status",
      this.formData.status === "rejected" || this.formData.status === "approved"
        ? "pending"
        : this.formData.status || "pending"
    );
    formData.append("remarks", this.formData.remarks || "");
    formData.append("location", this.formData.location || "");
    formData.append("latitude", this.formData.latitude || "");
    formData.append("longitude", this.formData.longitude || "");

    if (this.file) {
      formData.append("return_proof", this.file);
    }

    console.log("Sending data:", this.formData);
    axios
      .post("/api/update-dr-Rr", formData, {
        headers: { "Content-Type": "multipart/form-data" },
      })
      .then((response) => {
        console.log("Server Response:", response.data);
        Swal.fire("Success", "Delivery Return Updated Successfully", "success");
        this.$emit("closeModalReturnModal");
      })
      .catch((error) => {
        console.error("Update DR Error:", error);
        Swal.fire("Error", error.response?.data?.message || "Something went wrong!", "error");
      });
  },

  
},
};
</script>