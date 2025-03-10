<template>
    <div v-if="isVisible" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center z-50 font-roboto">
      <div class="bg-white p-8 rounded-md w-96">
        <h2 class="text-xl font-semibold mb-4">Edit User</h2>
        <form @submit.prevent="submitForm">
          <div class="mb-4">
            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
            <!-- Make the first_name input read-only -->
            <input type="text" id="first_name" v-model="localUser.first_name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" readonly />
          </div>
          <div class="mb-4">
            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
            <!-- Make the last_name input read-only -->
            <input type="text" id="last_name" v-model="localUser.last_name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" readonly />
          </div>
          <div class="mb-4">
            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
            <select id="role" v-model="localUser.role" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
              <option value="admin">Admin</option>
              <option value="manager">Manager</option>
              <option value="procurement">Procurement</option>
              <option value="warehouse_staff">Warehouse Staff</option>
            </select>
          </div>
          <div class="flex justify-end space-x-4">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Save Changes</button>
            <button type="button" @click="closeModal" class="bg-gray-300 text-black px-4 py-2 rounded-md">Cancel</button>
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