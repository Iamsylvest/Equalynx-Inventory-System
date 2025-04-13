<template>
  <div class="font-roboto">
    <!-- Button to Open Modal -->
    <button @click="showDrmodal = true" class="mt-11 h-[35px] sm:mt-5 md:mt-5 px-4 py-2 rounded-lg flex items-center justify-center font-semibold  dark:bg-green-700 dark:text-green-300 bg-custom-blue text-white drop-shadow-md whitespace-nowrap">
      <span>Add DR</span>
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 relative left-1">
        <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 9a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25V15a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25V9Z" clip-rule="evenodd" />
      </svg>
    </button>

    <div v-if="showDrmodal" class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 overflow-y-auto">
      <!-- Modal Overlay (Click to Close) -->
      <div @click="showDrmodal = false" class="absolute inset-0"></div>

      <!-- Modal Content -->
      <div class="relative bg-white shadow-lg rounded-md w-full [max-width:95rem] mx-auto mt-10 mb-10 dark:bg-custom-main">
        <!-- Header -->
        <div class="sticky top-0 bg-custom-blue p-4 rounded-t-md z-10 dark:bg-custom-table">
          <div class="flex items-center justify-between">
            <h1 class="text-lg text-white font-semibold flex m-auto ">
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
            <div class="bg-gray-100 p-6 rounded-md shadow-lg  dark:bg-custom-table dark:text-custom-white">
              <h2 class="font-medium text-gray-700 mb-6">General Information</h2>
              <div class="space-y-6">
                <div>
                  <label for="name" class="text-sm text-gray-600 dark:text-custom-white">Name:</label>
                  <input id="name" v-model="formData.name" type="text" class=" dark:bg-custom-table dark:focus:ring-white w-full p-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300">
                </div>
                <div>
                  <label for="project_name" class="text-sm text-gray-600 dark:text-custom-white">Project Name:</label>
                  <input id="project_name" v-model="formData.project_name" type="text" class="dark:bg-custom-table dark:focus:ring-white w-full p-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300">
                </div>
                <div>
                  <label for="status" class="text-sm text-gray-600 dark:text-custom-white">Status:</label>
                  <select 
                          id="status" 
                          v-model="formData.status" 
                          placeholder="Enter unit (e.g., 24 meters, 10 kg)"  
                          :disabled="true" 
                           class=" dark:bg-custom-table dark:focus:ring-white dark:text-custom-white w-full p-2 text-xs border border-gray-300 rounded-md bg-gray-100 text-gray-600 focus:ring-2 focus:ring-blue-300"
                        >
                          <option value="" disabled>Select status</option>
                          <option value="pending">pending</option>
                          <option value="approved">approved</option>
                          <option value="rejected">rejected</option>
                        </select>
                </div>
                <div>
                  <label for="remarks" class="text-sm text-gray-600 dark:text-custom-white">Remarks:</label>
                  <textarea id="remarks" v-model="formData.remarks" class=" dark:bg-custom-table dark:focus:ring-white w-full p-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300" rows="4" ></textarea>
                </div>
              </div>
            </div>

            <!-- Material Details Column -->
            <div class="bg-gray-100 p-6 rounded-md shadow-lg  dark:bg-custom-table">
              <h2 class="font-medium text-gray-700 mb-6 dark:text-custom-white">Material Details</h2>
              <div class="space-y-6">
                <!-- Iterate over materials dynamically -->
                <div v-for="(material, index) in formData.materials" :key="index" class="flex space-x-6">


                  <div class="flex-1">
                    <label :for="'material_' + index" class="text-sm text-gray-600 dark:text-custom-white">Material:</label>
                    <input
                     list="materialList" 
                    :id="'material_' + index" v-model="material.material_name" 
                    type="text" 
                    class=" dark:bg-custom-table dark:focus:ring-white w-full p-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300">
                    <datalist id="materialList">
                    <option v-for="(material, index) in materialName" :key="index" :value="material" ></option>
                  </datalist>
                  </div>
              


                  <div class="flex-1">
                    <label :for="'measurement_' + index" class="text-sm text-gray-600 dark:text-custom-white">Measurement:</label>
                    <input :id="'measurement_' + index" v-model="material.measurement"  placeholder="Enter unit (e.g., 24 meters, 10 kg)" class=" dark:bg-custom-table dark:focus:ring-white w-full p-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300">
                  </div>
                  <div class="flex-1">
                    <label :for="'unit' + index" class="text-sm text-gray-600 dark:text-custom-white">Unit:</label>
                        <select 
                          :id="'unit_' + index" 
                          v-model="material.unit" 
                          placeholder="Enter unit (e.g., 24 meters, 10 kg)" class=" dark:bg-custom-table dark:focus:ring-white w-full p-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300"
                        >
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
                  <div class="flex-1">
                    <label :for="'quantity_' + index" class="text-sm text-gray-600 dark:text-custom-white">Quantity:</label>
                    <input :id="'quantity_' + index" v-model="material.material_quantity" type="number" class=" dark:bg-custom-table dark:focus:ring-white w-full p-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300">
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
            <div class="bg-gray-100 p-6 rounded-md shadow-lg  dark:bg-custom-table">
              <h2 class="font-medium text-gray-700 mb-6">Location Details</h2>
              <div class="space-y-6">
                <div class="relative">
                        <label for="location" class="text-sm text-gray-600 dark:text-custom-white">Location:</label>
                        <input
                          id="location"
                          placeholder="Search the address"
                          @input="fetchLocationSuggestions"
                          v-model="formData.location"
                          type="text"
                          class="dark:bg-custom-table dark:focus:ring-white w-full p-2 text-xs border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300"
                        />

                        <!-- Suggestions dropdown -->
                        <ul v-if="locationSuggestions.length" class="absolute z-50 bg-white border text-balance border-gray-300 w-full mt-1 rounded-md shadow-md max-h-48 overflow-y-auto">
                          <li
                            v-for="(suggestion, index) in locationSuggestions"
                            :key="index"
                            @click="selectLocation(suggestion)"
                            class="p-2 text-sm hover:bg-blue-100 cursor-pointer"
                          >
                            {{ suggestion.formatted}}
                          </li>
                        </ul>
                      </div>
                      <div v-if="showMap" class="mt-4 relative z-10">
                    <div id="map" style="height: 400px; width: 100%;"></div>
                  </div>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex justify-center mt-8">
            <button type="submit" class="px-32 py-2 rounded-lg bg-custom-blue text-white dark:bg-custom-table dark:text-custom-white ">
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
import "leaflet-control-geocoder/dist/Control.Geocoder.css";
import "leaflet-control-geocoder";
  
  export default {
    data() {
      return {
        materialName: [],
        showDrmodal: false,
        map: null,
        marker: null,
        showMap: true, // Control to show map
        
          locationSuggestions: [],
          latitude: 14.5995,    // 👈 ADD THIS
          longitude: 120.9842,    // 👈 AND THIS

        formData: {
          name: '',
          project_name: '',
          status: 'pending',
          remarks: null,
          materials: [{ material_name: '', measurement: null, unit: '', material_quantity: null }],
          location: '',
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
          this.formData.materials.some(material => 
          !material.material_name?.trim() ||  // Ensure material name is not empty
            material.measurement === null ||    // Ensure measurement is not null (integer)
            material.unit?.trim() === '' ||     // Ensure unit is not empty
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
          this.formData.materials.push({ 
              material_name: '', 
              measurement: null,  // Integer field for measurement
              unit: '',           // String field for unit
              material_quantity: null 
          });
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
              materials: [{ 
                  material_name: '', 
                  measurement: null,  // Integer field for measurement
                  unit: '',           // String field for unit
                  material_quantity: null 
              }],
              location: '',
          };
          this.showMap = false;
      },
      async fetchLocationSuggestions() {
          if (!this.formData.location || this.formData.location.length < 2) {
            this.locationSuggestions = [];
            return;
          }

          const apiKey = 'aabf9bb2902248e99c7f4e2709bd7cff'; // Replace with your actual API key
            const url = `https://api.opencagedata.com/geocode/v1/json?key=${apiKey}&q=${encodeURIComponent(this.formData.location)}&limit=5`;
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
            this.formData.location = suggestion.formatted;

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
            
          initLeafletMap() {
            this.$nextTick(() => {
              const mapContainer = document.getElementById("map");

              if (mapContainer) {
                if (this.map) {
                  this.map.remove(); // Destroy existing map if it exists
                }

                // Initialize the map with the provided latitude and longitude
                this.map = L.map(mapContainer).setView([this.latitude, this.longitude], 20); // Default zoom level

                // Add OpenStreetMap tile layer (base map)
                L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                  attribution: "© OpenStreetMap contributors",
                }).addTo(this.map);

                // Add OpenCage Geocoder (search control)
                L.Control.geocoder({
                  geocoder: new L.Control.Geocoder.OpenCage('aabf9bb2902248e99c7f4e2709bd7cff'), // Your API Key
                }).addTo(this.map);

                // Add click event to the map
                this.map.on("click", this.onMapClick); // Listen for clicks on the map
              }
            });
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
                        this.formData.location = fullAddress;
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
                    console.log("Fetch Material names success");
                    this.materialName = response.data;
                    } catch(error){
                      console.error("Error fetching material names", error);
                    }
                  },
                },

    mounted(){
      this.fetchMaterialName();
      this.$nextTick(() => {
      this.initLeafletMap();
    });
    }
  };
  </script>
