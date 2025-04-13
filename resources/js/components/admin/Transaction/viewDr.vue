<template>
    <div class="font-roboto ">
      <div v-if="show" class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 overflow-y-auto">
        <!-- Modal Overlay (Click to Close) -->
        <div @click="$emit('close')" class="absolute inset-0"></div>

        <!-- Modal Content -->
        <div class="relative bg-white shadow-lg rounded-md w-full [max-width:95rem] mx-auto mt-10 mb-10">
          <!-- Header -->
          <div class="sticky top-0 bg-custom-blue dark:bg-custom-table p-4 rounded-t-md z-10">
            <div class="flex items-center justify-between">
              <h1 class="text-lg text-white font-semibold flex m-auto">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                VIEW DELIVERY RECEIPT #{{ item.dr_number }}
              </h1>
              <button @click="$emit('close')" class="text-white hover:text-gray-300 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>

          <!-- View-Only Content -->
          <div class="p-4 space-y-8 dark:bg-custom-main ">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
              <!-- General Information -->
              <div class="dark:bg-custom-table p-6 rounded-md shadow-lg">
                <h2 class="font-medium text-gray-700 mb-6  dark:text-custom-white">General Information</h2>
                <div class="space-y-6">
                  <div>
                    <label class="text-sm text-gray-600  dark:text-custom-white ">Name:</label>
                    <p class="text-xs bg-white p-2 border border-gray-300 rounded-md  dark:bg-custom-table">{{ item.name }}</p>
                  </div>
                  <div>
                    <label class="text-sm text-gray-600  dark:text-custom-white">Project Name:</label>
                    <p class="text-xs bg-white p-2 border border-gray-300 rounded-md  dark:bg-custom-table">{{ item.project_name }}</p>
                  </div>
                  <div>
                    <label class="text-sm text-gray-600  dark:text-custom-white">Status:</label>
                    <p class="text-xs bg-gray-100 p-2 border border-gray-300 rounded-md  dark:bg-custom-table">{{ item.status }}</p>
                  </div>
                  <div>
                    <label class="text-sm text-gray-600  dark:text-custom-white">Remarks:</label>
                    <p class="text-xs bg-white p-2 border border-gray-300 rounded-md  dark:bg-custom-table">{{ item.remarks }}</p>
                  </div>
                </div>
              </div>

              <!-- Material Details -->
              <div class="bg-gray-100 dark:bg-custom-table p-6 rounded-md shadow-lg">
                <div v-if="selectedmaterials.length === 0" class="text-gray-500 text-sm dark:text-custom-white">No materials available.</div>
                <h2 class="font-medium text-gray-700 mb-6 dark:text-custom-white">Materials Information</h2>
                <div v-for="(material, index) in selectedmaterials" :key="index" class="flex space-x-6">
                  <div class="flex-1 mb-3">
                    <label class="text-sm text-gray-600 dark:text-custom-white">Material:</label>
                    <p class="text-xs bg-white p-2 border border-gray-300 rounded-md w-full  dark:bg-custom-table" >{{ material.material_name || 'N/A' }}</p>
                  </div>
                  <div class="flex-1">
                    <label class="text-sm text-gray-600 dark:text-custom-white">Measurement:</label>
                    <p class="text-xs bg-white p-2 border border-gray-300 rounded-md w-full  dark:bg-custom-table" >{{ material.measurement || 'N/A' }}</p>
                  </div>
                  <div class="flex-1">
                    <label class="text-sm text-gray-600 dark:text-custom-white">Unit:</label>
                    <p class="text-xs bg-white p-2 border border-gray-300 rounded-md w-full  dark:bg-custom-table" >{{ material.unit || 'N/A' }}</p>
                  </div>
                  <div class="flex-1">
                    <label class="text-sm text-gray-600 dark:text-custom-white">Quantity:</label>
                    <p class="text-xs bg-white p-2 border border-gray-300 rounded-md w-full  dark:bg-custom-table" >{{ material.material_quantity || 'N/A' }}</p>
                  </div>
                    </div>
              </div>

        <!-- Location Details -->
                <div class="bg-gray-100 p-6 dark:bg-custom-table rounded-md shadow-lg">
                <h2 class="font-medium text-gray-700 mb-6 dark:text-custom-white">Location Details</h2>
                <div v-if="item" class="space-y-6">
                    <div>
                    <label class="text-sm text-gray-600 dark:text-custom-white">Location:</label>
                    <p class="text-xs bg-white p-2 border border-gray-300 rounded-md  dark:bg-custom-table">{{ item.location ?? 'N/A' }}</p>
                    </div>
                    <!-- Leaflet Map (Only show when lat/lng exist) -->
                    <div v-if="item.location" id="map" class="h-64 w-full mt-4" style="height: 400px; width: 100%;"></div>
                </div>
                </div>
            </div>


          </div>
        </div>
      </div>
    </div>
</template>
<script>
import "leaflet/dist/leaflet.css";
import L from 'leaflet'; // Ensure you import Leaflet
import "leaflet-control-geocoder/dist/Control.Geocoder.css";
import "leaflet-control-geocoder";

export default {
  props: {
    show: Boolean,
    item: {
      type: Object,
      default: () => ({})
    },
    materials: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      selectedmaterials: [],
      map: null, // Store Leaflet map instance
      marker:null,
      latitude: null,    // 👈 ADD THIS
      longitude:null,    // 👈 AND THIS
    };
  },
  watch: {
    item: {
      handler(newItem) {
        console.log("Updated item:", newItem);
        this.selectedmaterials = newItem?.materials || [];
        this.initLeafletMap(); // Load map when item updates
      },
      deep: true,
      immediate: true
    }
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
  }
};
</script>