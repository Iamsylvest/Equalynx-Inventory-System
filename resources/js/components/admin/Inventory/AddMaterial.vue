<template>
  <div>
    <!-- Button to Open Modal -->
    <button @click="showModal = true" class="relative top-[-80px] sm:static md:static lg:static w-[120px] h-[35px] rounded-lg  mt-5 flex items-center justify-center font-semibold bg-green-500 text-white dark:bg-green-700 dark:text-green-300 text-sm drop-shadow-md whitespace-nowrap">
      <span>Add Material</span>
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 relative left-1">
        <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 9a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25V15a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25V9Z" clip-rule="evenodd" />
      </svg>
    </button>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 flex items-center justify-center">
      <div @click="showModal = false" class="absolute inset-0"></div>
  
      <!-- Modal Content -->
      <div class="bg-white dark:bg-custom-main shadow-lg rounded-md max-w-2xl w-full mx-auto p-4 relative">
        <!-- Header -->
        <div class="bg-custom-blue  dark:bg-custom-table p-4 rounded-t-md flex justify-between items-center">
          <h1 class="text-lg text-white font-semibold flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
            </svg>
            ADD NEW MATERIAL
          </h1>
          <button @click="showModal = false" class="text-white hover:text-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-4 ">
          <div class="flex flex-col gap-4">
            <div>
                <label class="block text-gray-700 dark:text-custom-white">Material Name:</label>
                <input 
                  list="materialList" 
                  v-model="form.material_name"
                  type="text"
                  class="w-full border border-gray-300 px-2 py-1 focus:ring-2 focus:ring-blue-300 dark:focus:ring-white rounded-lg dark:bg-custom-table"
                  placeholder="Type or select a material"
                />
                <datalist id="materialList">
                  <option v-for="(material, index) in materialNames" :key="index" :value="material"></option>
                </datalist>
              </div>
            <div>
              <label class="block text-gray-700 dark:text-custom-white">Stocks:</label>
              <input v-model="form.stocks" type="text" placeholder="Type stocks" class="w-full border border-gray-300 px-2 py-1 focus:ring-2 focus:ring-blue-300 dark:focus:ring-white  rounded-lg  dark:bg-custom-table">
              </div>
     

            <div>
              <label class="block text-gray-700 dark:text-custom-white">Measurement:</label>
              <div class="flex">
                <!-- Number Input for Quantity -->
                <input 
                  type="number" 
                  step="1" 
                  v-model="form.measurement_quantity"
                  class="w-1/2 border border-gray-300 px-2 py-1 rounded-l-lg focus:ring-2 focus:ring-blue-300 dark:focus:ring-white dark:bg-custom-table"
                  placeholder="Enter quantity"
                  min="0"
                />

                <!-- Dropdown for Measurement Type -->
            <!-- Dropdown for Measurement Type -->
                <select
                  v-model="form.measurement_unit"
                  class="w-1/2 border border-gray-300 px-2 py-1 rounded-r-lg focus:ring-2 focus:ring-blue-300 dark:focus:ring-white dark:bg-custom-table"
                >
                  <option value="" disabled selected hidden>
                    Select a unit
                  </option>
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
          <button  @click="addMaterial" :disabled="loading"  class="px-4 py-2 rounded-lg bg-custom-blue text-white w-full   dark:bg-green-700 dark:text-green-300">
          <span v-if="!loading">Add Material</span> 
          <span v-else class="flex items-center justify-center">
              <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 1 0 16 0H4z"></path>
              </svg>
              Loading...
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Swal from 'sweetalert2';
import axios from 'axios';

export default {
  data() {
    return {
      showModal: false,
      loading: false,
      form: {
        material_name: '',
        stocks: '',
        measurement_quantity: '',
        measurement_unit: '',
      },
      materialNames: [], // Stores fetched material names
    };
  },
  
  methods: {
    async addMaterial() {
      if (this.loading) return; // prevent multiple request
        this.loading = true; // Start loading state
      
      // Basic validation
      if (!this.form.material_name || !this.form.stocks || !this.form.measurement_quantity || !this.form.measurement_unit) {
        Swal.fire({
                title: 'Warning!',
                text: 'Please fill in all the required fields before submitting.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return; // Prevent submission if any field is empty
      }

      try {
        
        // Step 1: Check if the material already exists
        const checkResponse = await axios.get(`/api/inventory/check?material_name=${this.form.material_name}&measurement_quantity=${this.form.measurement_quantity}&measurement_unit=${this.form.measurement_unit}`);
          
        // Step 2: If the material exists, show an error and prevent addition
        if (checkResponse.data.exists) {
          Swal.fire({
            title: 'Error!',
            text: 'This material already exists. You can edit the existing one.',
            icon: 'error',
            confirmButtonText: 'OK'
          });
          return; // Stop the process if material exists
        }

        // Step 3: If the material doesn't exist, proceed with adding it
        const response = await axios.post('/api/inventory', this.form);
        
        // Emit to parent component to update material list in real-time
        this.$emit('materialAdded', response.data.inventory);
        this.showModal = false; // Close the modal in the child
        // Step 4: Show success alert
        Swal.fire({
          title: 'Success!',
          text: 'Material added successfully.',
          icon: 'success',
          confirmButtonText: 'OK'
        });

        // Close the modal after submitting
        this.showModal = false;
      } catch (error) {
        console.error('Error adding material:', error);
        Swal.fire("Error!", "Failed to add material.", "error");
      }
      finally{
        this.loading = false;
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
  mounted(){
    this.fetchMaterialName()
  }

};
</script>