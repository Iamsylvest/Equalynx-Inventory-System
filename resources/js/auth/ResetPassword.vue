<template>
  <div class="h-screen flex justify-center items-center bg-gray-100">
    <div class="p-8 bg-white w-full max-w-md rounded-lg shadow-lg">
      <h2 class="text-2xl font-semibold text-center mb-6">Reset Password</h2>
      <form @submit.prevent="resetPassword">
        <div class="mb-4">
          <label for="username" class="block text-sm font-medium text-gray-700">Username:</label>
          <input
            type="text"
            id="username"
            v-model="username"
            class="mt-1 p-3 w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          />
        </div>
        <div class="mb-4">
          <label for="password" class="block text-sm font-medium text-gray-700">New Password:</label>
          <input
            type="password"
            id="password"
            v-model="password"
            class="mt-1 p-3 w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          />
        </div>
        <button
          type="submit"
          class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400"
        >
          Reset Password
        </button>
      </form>
    </div>
  </div>
</template>
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        username: '',
        password: '',
      };
    },
    methods: {
      async resetPassword() {
        try {
          const response = await axios.post('/api/reset-password', {
            username: this.username,
            password: this.password,
          });
  
          alert(response.data.message); // Display success message
          this.$router.push('/login'); // Redirect to login page
        } catch (error) {
          console.error(error.response.data.message || 'An error occurred');
          alert(error.response.data.message || 'Failed to reset password');
        }
      },
    },
  };
  </script>