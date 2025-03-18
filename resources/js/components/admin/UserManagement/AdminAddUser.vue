<template>
  <div class="font-roboto ">
    <!-- Add User Button -->
    <button 
        @click="showModal = true" 
        class="ml-[-25px] w-[240px] md:w-full lg:w-full sm:ml-[-25px] md:ml-0 lg:ml-0 px-4 py-2 rounded-lg flex items-center justify-center font-semibold bg-[#73EC8B] text-white drop-shadow-md whitespace-nowrap">
        <span>Add User</span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 relative left-1">
          <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 9a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25V15a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25V9Z" clip-rule="evenodd" />
        </svg>
    </button>
    
        <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
      <!-- Modal Overlay -->
      <div @click="showModal = false" class="absolute inset-0 z-10 bg-gray-800 opacity-50"></div>

      <!-- Modal Content -->
      <div class="bg-white shadow rounded-md px-6 py-6 z-20 max-w-md w-full mx-6">
        <!-- Modal Header -->
        <div class="flex items-center justify-between mb-4">
          <h1 class="text-lg font-semibold">User Management > Add User</h1>
          <button @click="showModal = false" class="flex items-center justify-center p-2 hover:bg-gray-100 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Account Details -->
        <div class="border-b mb-3">
          <h1 class="font-bold text-sm">Account Details</h1>
          <div class="grid grid-cols-1 gap-3">
            <div>
              <p class="text-xs">Firstname:</p>
              <input v-model="form.first_name" type="text" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"/>
            </div>
            <div>
              <p class="text-xs">Middlename:</p>
              <input v-model="form.middle_name" type="text" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"/>
            </div>
            <div>
              <p class="text-xs">Lastname:</p>
              <input v-model="form.last_name" type="text"  class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"/>
            </div>
            <div>
              <p class="text-xs">Email:</p>
              <input v-model="form.email" type="email" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"/>
            </div>
            <div class="relative">
                  <p class="text-xs">Password:</p>
                  <input 
                    v-model="form.password" 
                    :type="showPassword ? 'text' : 'password'" 
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
                  />
                  <button 
                    type="button" 
                    @click="togglePasswordVisibility" 
                    class="absolute top-10 right-3 transform -translate-y-1/2 text-sm text-blue-500 hover:text-blue-700"
                  >
                    {{ showPassword ? 'Hide' : 'Show' }}
                  </button>
                </div>
            <div>
              <p class="text-xs">Role:</p>
              <select v-model="form.role" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                <option value="" disabled selected>Select Role</option>
                <option value="admin">Admin</option>
                <option value="manager">Manager</option>
                <option value="procurement">Procurement</option>
                <option value="warehouse_staff">Warehouse Staff</option>
              </select>
            </div>

          </div>
        </div>

              <!-- Save Button -->
        <div class="text-center">
          <button 
            @click="addUser" 
            :disabled="loading"
            class="w-full py-2 rounded-lg font-semibold bg-custom-blue text-white drop-shadow-md text-sm disabled:bg-gray-400 disabled:cursor-not-allowed"
          >
            <span v-if="!loading">Add User</span>
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
import axios from 'axios';
import Swal from 'sweetalert2';
export default {
  data() {
    return {
      showModal: false,
      loading: false, // Added loading state
      showPassword: false,
      form: {
        first_name: '',
        middle_name: '',
        last_name: '',
        email: '',
        role: '',
        password: '',
      }
    };
  }, 
    methods: {
    async addUser() {
      if (this.loading) return; // Prevent multiple requests
      this.loading = true; // Start loading state
      try {
        const response = await axios.post('/api/users', this.form);
        this.$emit('userAdded', response.data.user);
        this.showModal = false;

        // Show success alert
        Swal.fire({
          title: 'User Added!',
          text: 'The user has been successfully added.',
          icon: 'success',
          confirmButtonText: 'OK'
        });

      } catch (error) {
        console.error('Error adding user:', error);
        Swal.fire({
          title: 'Error!',
          text: 'Failed to add user. Please try again.',
          icon: 'error',
          confirmButtonText: 'OK'
        });
      }
      finally {
        this.loading = false; // End loading state
      }
    },
    togglePasswordVisibility() {
      this.showPassword = !this.showPassword;
    }
  }
};
</script>