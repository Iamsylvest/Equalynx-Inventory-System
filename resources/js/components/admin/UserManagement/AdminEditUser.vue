<template>
        <!-- Modal Overlay -->
    <div v-if="isVisible" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center z-50 font-roboto">
      <div class="bg-white dark:bg-custom-main p-8 rounded-md w-96">
       <div class="flex justify-between">
        <h2 class="text-xl font-semibold mb-4">Edit User</h2>
        <button type="button" @click="closeModal" class="flex items-center justify-center p-2 dark:hover:bg-custom-hover hover:bg-gray-100 rounded-full">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
       </div>
        <form @submit.prevent="submitForm">
          <div class="mb-4">
            <label for="first_name" class="block text-sm font-medium text-gray-700">Email</label>
            <!-- Make the first_name input read-only -->
            <input type="text" id="first_name" v-model="localUser.email" class=" dark:bg-custom-table mt-1 block w-full px-3 py-2 border  border-gray-300 rounded-md"/>
          </div>
          <div class="mb-4">
            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
            <!-- Make the first_name input read-only -->
            <input type="text" id="first_name" v-model="localUser.first_name" class="  dark:bg-custom-table mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"  />
          </div>
          <div class="mb-4">
            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
            <!-- Make the last_name input read-only -->
            <input type="text" id="last_name" v-model="localUser.last_name" class=" dark:bg-custom-table mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"  />
          </div>
          <div class="mb-4">
            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
            <select id="role" v-model="localUser.role" class=" dark:bg-custom-table mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
              <option value="admin">Admin</option>
              <option value="manager">Manager</option>
              <option value="procurement">Procurement</option>
              <option value="warehouse_staff">Warehouse Staff</option>
            </select>
          </div>
          <div class="flex justify-end space-x-4">
            <button type="submit" class=" bg-green-500 text-white dark:bg-green-700 dark:text-green-300 px-4 py-2 w-full rounded-md">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      isVisible: Boolean,
      user: Object
    },
    data() {
      return {
        // Make sure to clone the user object to avoid mutating the prop directly
        localUser: { ...this.user },  // Initialize with a copy of the user data
      };
    },
    watch: {
      // Watch for changes to the user prop in case it's updated in the parent component
      user: {
        handler(newUser) {
          this.localUser = { ...newUser };  // Update local copy if the user prop changes
        },
        deep: true,
      },
    },
    methods: {
      closeModal() {
        this.$emit('close');  // Emit an event to close the modal
      },
      submitForm() {
        // Emit the updated user to the parent when "Save Changes" is clicked
        this.$emit('update', this.localUser);
      }
    }
  };
  </script>