<template>
  <div class="h-screen bg-cover bg-center relative" :style="{ backgroundImage: `url(${backgroundImage})` }">
    <div class="absolute inset-0 bg-opacity-50 flex justify-center items-center">
      <div class="p-9 bg-white w-full max-w-md h-auto rounded-lg shadow-lg sm:mx-4 md:mx-8 lg:mx-16 xl:mx-32">
        <form @submit.prevent="handleLogin">
          <div class="flex justify-center mb-6">
            <img src="@/assets/EqualynxLogo.png" alt="Equalynx Logo" class="w-32 h-32 object-contain" />
          </div>

          <div class="flex flex-col mb-4">
            <label for="email" class="text-sm font-medium text-gray-700">Email:</label>
            <input
              type="email"
              id="email"
              v-model="email"
              class="mt-1 p-3 border border-gray-300 rounded-md"
              required
            />
          </div>
          <div class="flex flex-col mb-6 relative">
            <label for="password" class="text-sm font-medium text-gray-700">Password:</label>
            <input
              :type="showPassword ? 'text' : 'password'"
              id="password"
              v-model="password"
              class="mt-1 p-3 border border-gray-300 rounded-md"
              required
            />
            <button type="button" @click="togglePasswordVisibility" class="absolute top-9 right-3">
              {{ showPassword ? 'Hide' : 'Show' }}
            </button>
          </div>

          <div class="flex justify-end">
            <button
              type="submit"
              class="w-full px-4 py-3 bg-blue-600 text-white rounded-md"
            >
              Login
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import backgroundImage from '@/assets/dataCenter.jpg';
import LoadingSpinner from '../components/LoadingSpinner.vue';

export default {
  name: 'LoginPage',
  components: {
    LoadingSpinner,
  },
  beforeRouteEnter(to, from, next) {
    next(() => {
      if (sessionStorage.getItem('reloaded') !== 'true') {
        sessionStorage.setItem('reloaded', 'true');
        window.location.reload();
      } else {
        sessionStorage.removeItem('reloaded');
      }
    });
  },

  data() {
    return {
      backgroundImage,
      email: '',
      password: '',
      showPassword: false,
      isLoading: false, // Fixed variable name
    };
  },
  methods: {
    ...mapActions('auth', ['login']), // Map the login action from Vuex

    togglePasswordVisibility() {
      this.showPassword = !this.showPassword;
    },

    async handleLogin() {
      this.isLoading = true; // Show the spinner

      try {
        await this.login({ email: this.email, password: this.password });

        // Redirect to dashboard after successful login
        this.$router.push('/UserManagement');
      } catch (error) {
        alert(error.message);
      } finally {
        this.isLoading = false; // Hide the spinner
      }
    },
  },
};
</script>