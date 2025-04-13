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
                <div class="bg-gray-100 dark:bg-custom-table p-6 rounded-md shadow-lg relative">
                  <h2 class="font-medium text-gray-700 mb-6 dark:text-custom-white">Location Details</h2>
                  <div class="space-y-4">
                    <div>
                      <label class="text-sm text-gray-600 dark:text-custom-white">Location:</label>
                      <input
                        v-model="item.location"
                        @input="fetchLocationSuggestions"
                        :disabled="userRole !== 'procurement'"
                        class="text-xs p-2 border border-gray-300 rounded-md w-full dark:bg-custom-table dark:text-custom-white"
                        :class="userRole !== 'procurement' ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white text-black'"
                      />
                    </div>

                    <!-- Suggestions dropdown -->
                    <ul v-if="locationSuggestions.length" class="absolute z-20 bg-white border border-gray-300 w-full mt-1 rounded-md shadow-md max-h-48 overflow-y-auto">
                      <li
                        v-for="(suggestion, index) in locationSuggestions"
                        :key="index"
                        @click="selectLocation(suggestion)"
                        class="p-2 text-sm hover:bg-blue-100 cursor-pointer"
                      >
                        {{ suggestion.formatted }}
                      </li>
                    </ul>
                  </div>

                  <!-- Leaflet Map (Only show when lat/lng exist) -->
                  <div v-if="showMap" class="mt-4 relative z-10">
                    <div id="map" style="height: 400px; width: 100%;"></div>
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
import "leaflet-control-geocoder/dist/Control.Geocoder.css";
import "leaflet-control-geocoder";
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
        locationSuggestions: [],
        latitude: null,    // 👈 ADD THIS
        longitude: null,    // 👈 AND THIS
        map: null,
        marker: null,
        showMap: true, // Control to show map
      }
    },
    mounted() {
      this.fetchMaterialName();
    },
    watch: {
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

            this.initLeafletMap();
       
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
  async fetchLocationSuggestions() {
          if (!this.item.location || this.item.location.length < 2) {
            this.locationSuggestions = [];
            return;
          }

          const apiKey = 'aabf9bb2902248e99c7f4e2709bd7cff'; // Replace with your actual API key
            const url = `https://api.opencagedata.com/geocode/v1/json?key=${apiKey}&q=${encodeURIComponent(this.item.location)}&limit=5`;
          try {
            const response = await fetch(url);
            const data = await response.json();

            if (data.results && data.results.length) {
              this.locationSuggestions = data.results;
            } else {
              console.log("No results found");
              this.locationSuggestions = [];
           
            }
          } catch (error) {
            console.error("Failed to fetch location suggestions:", error);
          }
        },

        async selectLocation(suggestion) {
            // Set the location text to the display name of the suggestion
            this.item.location = suggestion.formatted;

            // Correctly extract latitude and longitude from the 'geometry' object
            const lat = parseFloat(suggestion.geometry.lat);
            const lon = parseFloat(suggestion.geometry.lng);

            // Validate latitude and longitude
            if (isNaN(lat) || isNaN(lon)) {
              console.error("Invalid latitude or longitude received:", suggestion.geometry.lat, suggestion.geometry.lng);
              return; // Return early if lat or lon is invalid
            }

            // Set latitude and longitude
            this.latitude = lat;
            this.longitude = lon;

            // Clear location suggestions
            this.locationSuggestions = [];

            // Show the map
            this.showMap = true;

            // Initialize the map if it's not already initialized
            if (!this.map) {
              this.initLeafletMap();
            }

            // Wait for the next DOM update
            await this.$nextTick();

            // Update the map marker if the map and coordinates are valid
            if (this.map && this.latitude && this.longitude) {
              this.updateMapMarker(this.latitude, this.longitude);
            } else {
              console.error("Map or coordinates are not initialized correctly.");
            }
          },
            
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
                      this.map = L.map(mapContainer).setView([this.latitude, this.longitude], 15); // Default zoom level

                      // Add OpenStreetMap tile layer (base map)
                      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                        attribution: "© OpenStreetMap contributors",
                      }).addTo(this.map);

                      // Add a marker at the location (if exists)
                      this.marker = L.marker([this.latitude, this.longitude]).addTo(this.map);
                      this.map.on("click", this.onMapClick); // Listen for clicks on the map
                    }
                  });
                } else {
                  console.error("Location not found for the address");
                }
              } catch (error) {
                console.error("Failed to fetch location coordinates:", error);
              }
            },

                    // Handle map click event
              onMapClick(event) {
                const { lat, lng } = event.latlng; // Get the clicked latitude and longitude
                this.latitude = lat;
                this.longitude = lng;

                // Only update marker if lat/lng are valid numbers
                if (!isNaN(lat) && !isNaN(lng)) {
                  // Move the marker to the clicked position
                  this.updateMapMarker(lat, lng);

                  // Reverse geocode the coordinates
                  this.reverseGeocode(lat, lng);
                } else {
                  console.error("Invalid LatLng object:", lat, lng);
                }
              },
                // Reverse geocode using OpenCage API
                reverseGeocode(lat, lng) {
                  // URL encode the lat and lng values to ensure they are safely included in the URL
                  const apiUrl = `https://api.opencagedata.com/geocode/v1/json?key=aabf9bb2902248e99c7f4e2709bd7cff&q=${encodeURIComponent(lat + ' ' + lng)}&pretty=1`;

                  fetch(apiUrl)
                    .then(response => response.json())
                    .then(data => {
                      if (data && data.results && data.results.length > 0) {
                        const fullAddress = data.results[0].formatted;
                        this.item.location = fullAddress;
                      } else {
                        console.log("No results found for the given coordinates.");
                      }
                    })
                    .catch(error => {
                      console.error("Geocoding error:", error);
                    });
                },
  
            updateMapMarker(lat, lon) {
              if (!this.map) {
                console.error("Map is not initialized yet.");
                return;
              }

              if (this.marker) {
                this.marker.setLatLng([lat, lon]);
              } else {
                this.marker = L.marker([lat, lon]).addTo(this.map);
              }

              this.map.setView([lat, lon], 16);
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