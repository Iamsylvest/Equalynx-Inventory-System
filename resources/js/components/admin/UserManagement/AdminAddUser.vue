<template>
  <div>
    <!-- Add User Button -->
    <button 
        @click="showModal = true" 
        class="px-4 py-2 rounded-lg flex items-center justify-center font-semibold bg-green-500 hover:bg-green-400 drop-shadow-md whitespace-nowrap w-full">
        <span>Add User</span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 relative left-1">
          <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 9a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25V15a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25V9Z" clip-rule="evenodd" />
        </svg>
    </button>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
      <!-- Modal Overlay -->
      <div @click="showModal = false" class="absolute inset-0 z-10 bg-gray-800 opacity-50"></div>

      <!-- Modal Content -->
      <div class="bg-white shadow rounded-md px-4 py-10 z-20 max-w-4xl w-full mx-4">
        <!-- Modal Header -->
        <div class="flex items-center justify-between mb-4">
          <h1 class="text-xl font-semibold">User Management > Add User</h1>
          <button @click="showModal = false" class="flex items-center justify-center p-2 hover:bg-gray-100 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Account Details -->
        <div class="border-b pb-4 mb-4">
          <h1 class="font-bold">Account Details</h1>
          <br>
          <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <p>Firstname:</p>
              <input v-model="form.first_name" type="text" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"/>
            </div>
            <div>
              <p>Middlename:</p>
              <input v-model="form.middle_name" type="text" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"/>
            </div>
            <div>
              <p>Lastname:</p>
              <input v-model="form.last_name" type="text" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"/>
            </div>
            <div>
              <p>Email:</p>
              <input v-model="form.email" type="email" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"/>
            </div>
            <div>
              <p>Password:</p>
              <input v-model="form.password" type="password" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"/>
            </div>
            <div>
              <p>Role:</p>
              <select v-model="form.role" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                <option value="" disabled selected>Select Role</option>
                <option value="admin">Admin</option>
                <option value="manager">Manager</option>
                <option value="procurement">Procurement</option>
                <option value="warehouse_staff">Warehouse_Staff</option>
              </select>
            </div>
            <div>
              <p>Status:</p>
              <select v-model="form.is_active" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                <option value="" disabled selected>Select Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>
          </div>
        </div>
        <br>

        <!-- Save Button -->
        <div class="text-center">
          <button @click="addUser" class="px-6 py-2 rounded-lg font-semibold bg-blue-500 text-white hover:bg-blue-400 drop-shadow-md">
            Add User
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data() {
    return {
      showModal: false,
      form: {
        first_name: '',
        middle_name: '',
        last_name: '',
        email: '',
        role: '',
        is_active: true,
        password: '',
      }
    };
  },
  methods: {
    async addUser() {
      try {
        // Send the form data to your backend API
        const response = await axios.post('/api/users', this.form);
        this.showModal = false; // Close the modal
        this.$emit('userAdded', response.data.user); // Emit event or handle response
        alert(response.data.message); // Show success message
      } catch (error) {
        alert(error.response.data.message || 'Something went wrong');
      }
    }
  }
};
</script>