<template>
  <div class="h-screen flex justify-center items-center bg-gray-100">
    <div class="p-8 bg-white w-full max-w-md rounded-lg shadow-lg">
      <h2 class="text-2xl font-semibold text-center mb-6">Forgot Password</h2>
      <form @submit.prevent="sendResetLink">
        <div class="mb-4">
          <label for="email" class="block text-sm font-medium text-gray-700">Email Address:</label>
          <input
            type="email"
            id="email"
            v-model="email"
            class="mt-1 p-3 w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          />
        </div>
        <button
          type="submit"
          class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400"
        >
          Send Reset Link
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
      email: '',
    };
  },
  methods: {
    async sendResetLink() {
      if (!this.email) {
        alert("Please enter an email address.");
        return;
      }

      try {
        const response = await axios.post('/api/forgot-password', {
          email: this.email,
        });

        alert(response.data.message); // Display success message
        this.$router.push('/login'); // Redirect to login page after sending reset link
      } catch (error) {
        console.error(error.response.data.message || 'An error occurred');
        alert(error.response.data.message || 'Failed to send reset link');
      }
    },
  },
};
</script>