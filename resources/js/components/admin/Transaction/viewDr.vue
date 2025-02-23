<template>
    <div class="font-roboto">
      <div v-if="show" class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 overflow-y-auto">
        <!-- Modal Overlay (Click to Close) -->
        <div @click="$emit('close')" class="absolute inset-0"></div>

        <!-- Modal Content -->
        <div class="relative bg-white shadow-lg rounded-md w-full [max-width:95rem] mx-auto mt-10 mb-10">
          <!-- Header -->
          <div class="sticky top-0 bg-custom-blue p-4 rounded-t-md z-10">
            <div class="flex items-center justify-between">
              <h1 class="text-lg text-white font-semibold flex m-auto">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                VIEW DELIVERY RECEIPT
              </h1>
              <button @click="$emit('close')" class="text-white hover:text-gray-300 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>

          <!-- View-Only Content -->
          <div class="p-8 space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
              <!-- General Information -->
              <div class="bg-gray-100 p-6 rounded-md shadow-lg">
                <h2 class="font-medium text-gray-700 mb-6">General Information</h2>
                <div class="space-y-6">
                  <div>
                    <label class="text-sm text-gray-600">Name:</label>
                    <p class="text-xs bg-white p-2 border border-gray-300 rounded-md">{{ item.name }}</p>
                  </div>
                  <div>
                    <label class="text-sm text-gray-600">Project Name:</label>
                    <p class="text-xs bg-white p-2 border border-gray-300 rounded-md">{{ item.project_name }}</p>
                  </div>
                  <div>
                    <label class="text-sm text-gray-600">Status:</label>
                    <p class="text-xs bg-gray-100 p-2 border border-gray-300 rounded-md">{{ item.status }}</p>
                  </div>
                  <div>
                    <label class="text-sm text-gray-600">Remarks:</label>
                    <p class="text-xs bg-white p-2 border border-gray-300 rounded-md">{{ item.remarks }}</p>
                  </div>
                </div>
              </div>

              <!-- Material Details -->
              <div class="bg-gray-100 p-6 rounded-md shadow-lg">
                <div v-if="selectedmaterials.length === 0" class="text-gray-500 text-sm">No materials available.</div>
                <h2 class="font-medium text-gray-700 mb-6">Materials Information</h2>
                <div v-for="(material, index) in selectedmaterials" :key="index" class="flex space-x-6">
                  <div class="flex-1 mb-3">
                    <label class="text-sm text-gray-600">Material:</label>
                    <p class="text-xs bg-white p-2 border border-gray-300 rounded-md w-full" >{{ material.material_name || 'N/A' }}</p>
                  </div>
                  <div class="flex-1">
                    <label class="text-sm text-gray-600">Measurement:</label>
                    <p class="text-xs bg-white p-2 border border-gray-300 rounded-md w-full" >{{ material.measurement || 'N/A' }}</p>
                  </div>
                  <div class="flex-1">
                    <label class="text-sm text-gray-600">Unit:</label>
                    <p class="text-xs bg-white p-2 border border-gray-300 rounded-md w-full" >{{ material.unit || 'N/A' }}</p>
                  </div>
                  <div class="flex-1">
                    <label class="text-sm text-gray-600">Quantity:</label>
                    <p class="text-xs bg-white p-2 border border-gray-300 rounded-md w-full" >{{ material.material_quantity || 'N/A' }}</p>
                  </div>
                    </div>
              </div>

        <!-- Location Details -->
                <div class="bg-gray-100 p-6 rounded-md shadow-lg">
                <h2 class="font-medium text-gray-700 mb-6">Location Details</h2>
                <div v-if="item" class="space-y-6">
                    <div>
                    <label class="text-sm text-gray-600">Location:</label>
                    <p class="text-xs bg-white p-2 border border-gray-300 rounded-md">{{ item.location ?? 'N/A' }}</p>
                    </div>
                    <div class="flex justify-between">
                    <div class="w-1/2 pr-3">
                        <label class="text-sm text-gray-600">Latitude:</label>
                        <p class="text-xs bg-white p-2 border border-gray-300 rounded-md">{{ item.latitude ?? 'N/A' }}</p>
                    </div>
                    <div class="w-1/2 pl-3">
                        <label class="text-sm text-gray-600">Longitude:</label>
                        <p class="text-xs bg-white p-2 border border-gray-300 rounded-md">{{ item.longitude ?? 'N/A' }}</p>
                    </div>
                    </div>

                    <!-- Leaflet Map (Only show when lat/lng exist) -->
                    <div v-if="item.latitude && item.longitude" id="map" class="h-64 w-full mt-4"></div>
                </div>
                <div v-else>
                    <p class="text-sm text-gray-500">Loading location details...</p>
                </div>
                </div>
            </div>

            <!-- Close Button -->
            <div class="flex justify-center mt-8">
              <button @click="$emit('close')" class="px-32 py-2 rounded-lg bg-custom-blue text-white">
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>
<script>
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
      map: null // Store Leaflet map instance
    };
  },
  watch: {
    item: {
      handler(newItem) {
        console.log("Updated item:", newItem);
        this.selectedmaterials = newItem?.materials || [];
        this.loadMap(); // Load map when item updates
      },
      deep: true,
      immediate: true
    }
  },
  methods: {
    loadMap() {
      if (this.item.latitude && this.item.longitude) {
        this.$nextTick(() => {
          if (!this.map) {
            // Initialize map if it doesn't exist
            this.map = L.map("map").setView(
              [this.item.latitude, this.item.longitude],
              15
            );

            // Add tile layer (OpenStreetMap)
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
              attribution: "&copy; OpenStreetMap contributors"
            }).addTo(this.map);

            // Add marker
            L.marker([this.item.latitude, this.item.longitude])
              .addTo(this.map)
              .openPopup();
          } else {
            // Update map if it already exists
            this.map.setView(
              [this.item.latitude, this.item.longitude],
              15
            );
          }
        });
      }
    }
  }
};
</script>