<template>
    <div>
   <!-- Modal -->
      <div v-if="isVisible" class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 flex items-center justify-center">
        <!-- Modal Content -->
        <div class="bg-white shadow-lg rounded-md max-w-2xl w-full mx-auto p-4 relative">
          <!-- Header -->
          <div class="bg-custom-blue p-4 rounded-t-md flex justify-between items-center">
            <h1 class="text-lg text-white font-semibold flex items-center">
              <svg  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
              </svg>
             EDIT MATERIAL
            </h1>
            <button @click="$emit('closeModal')" class="text-white hover:text-gray-300">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
  
          <!-- Modal Body -->
          <div class="p-4">
            <div class="flex flex-col gap-4">
              <div>
                <label class="block text-gray-700">Material Name:</label>
                <input v-model="editedMaterial.material_name" type="text"   :readonly="true"  class="w-full border border-gray-300 px-2 py-1 focus:ring-2 focus:ring-blue-300  rounded-lg">
              </div>
              <div>
                <label class="block text-gray-700">Stocks:</label>
                <input v-model="editedMaterial.stocks" type="text" class="w-full border border-gray-300 px-2 py-1 focus:ring-2 focus:ring-blue-300  rounded-lg">
              </div>
              <div>
                <label class="block text-gray-700">Threshold:</label>
                <input v-model="editedMaterial.threshold" type="text"  class="w-full border border-gray-300 px-2 py-1 focus:ring-2 focus:ring-blue-300  rounded-lg "  >
              </div>
  
              <div>
                <label class="block text-gray-700">Measurement:</label>
                <div class="flex">
                  <!-- Number Input for Quantity -->
                  <input 
                    type="number" 
                    v-model="editedMaterial.measurement_quantity"
                    
                    class="w-1/2 border border-gray-300 px-2 py-1 rounded-l-lg focus:ring-2 focus:ring-blue-300"
                    placeholder="Enter quantity"
                    min="0"
                  />
  
                  <!-- Dropdown for Measurement Type -->
                  <select
                    v-model="editedMaterial.measurement_unit"
                    class="w-1/2 border border-gray-300 px-2 py-1 rounded-r-lg focus:ring-2 focus:ring-blue-300"
                  >
                    <option value="pcs">Pieces (pcs)</option>
                    <option value="m">Meters (m)</option>
                    <option value="cm">Centimeters (cm)</option>
                    <option value="in">Inches (in)</option>
                    <option value="kg">Kilograms (kg)</option>
                    <option value="g">Grams (g)</option>
                    <option value="l">Liters (L)</option>
                    <option value="ml">Milliliters (mL)</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Submit Button -->
          <div class="flex justify-end p-4">
            <button @click="updateMaterial" class="px-4 py-2 rounded-lg bg-custom-blue text-white w-full">Add Material</button>
          </div>
        </div>
      </div>
    </div>
</template>
  
<script>
export default {
  props: {
    isVisible: Boolean,
    // Receiving the selected material data from the parent component // Receiving an object named "material" from the parent
    material: {
    type: Object,
    required: true, 
  },
  },
  data() {
    return {
      // Creating a local copy of the material object to avoid directly mutating the prop
      // Update local material copy when prop changes
      editedMaterial: { ...this.material },
    };
  },
  watch: {
    // Watching for changes in the material prop (e.g., when selecting a different material)
    material: {
      handler(newMaterial) {
        // Updating the local editedMaterial whenever the prop changes
        this.editedMaterial = { ...newMaterial };
      },
      deep: true, // Ensures deep watching for nested object changes
    },
  },
  methods: {
    // Function to emit the updated material data back to the parent component
    updateMaterial() {
      this.$emit('update', this.editedMaterial);  // Emit the updated material
      this.closeModal();  // Close the modal after saving
    },
    // Function to emit an event to close the modal (handled by the parent)
    closeModal() {
      this.$emit('closeModal');
    },
  },
};
</script>