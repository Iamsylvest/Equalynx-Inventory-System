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
              :disabled="isLoading" 
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
import Swal from 'sweetalert2'; // Import SweetAlert2


export default {
  name: 'LoginPage',
  components: {},
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
      isLoading: false, // Variable to track loading state
    };
  },
  methods: {
    ...mapActions('auth', ['login']), // Map the login action from Vuex

    async handleLogin() {
      this.isLoading = true; // Show the loading state

      // Show the SweetAlert loading spinner
      Swal.fire({
        title: 'Logging in...',
        text: 'Please wait while we process your login.',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      try {
        const userRole = await this.login({email: this.email, password: this.password});

        if (userRole === 'admin'){
          this.$router.push("/UserManagement");
        } else if (userRole === 'manager'){
          this.$router.push("/AdminTransaction");
        } else if (userRole === 'warehouse_staff'){
          this.$router.push("/AdminInventory");
        } 
        else if (userRole === 'procurement'){
          this.$router.push("/UserManagement");
        } 
         else {
            this.$router.push("/"); // Default fallback
          }
      } catch (error) {
        Swal.fire({
          title: 'Error!',
          text: error.message || 'Something went wrong. Please try again.',
          icon: 'error',
        });
      } finally {
        this.isLoading = false; // Hide the spinner
        Swal.close(); // Close the SweetAlert spinner
      }
    },

    togglePasswordVisibility() {
      this.showPassword = !this.showPassword;
    }
  },
};
</script>