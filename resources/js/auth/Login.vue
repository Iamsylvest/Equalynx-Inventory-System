<template>
  <div class="flex flex-col items-center justify-center h-screen gap-4 bg-cover bg-center relative" :style="{ backgroundImage: `url(${backgroundImage})` }">
    <!-- Login Form -->
    <div v-if="!otpVerified" class="flex flex-col p-9 gap-4 bg-white w-full max-w-md h-auto rounded-lg shadow-lg sm:mx-4 md:mx-8 lg:mx-16 xl:mx-32">
      <div class="flex justify-center mb-6">
            <img src="@/assets/EqualynxLogo.png" alt="Equalynx Logo" class="w-32 h-32 object-contain"  />
        </div>

        <div class="flex flex-col mb-4">
            <label for="email" class="text-sm font-medium text-gray-700 ">Email:</label>
            <input
              type="email"
              id="email"
              v-model="email"
              class="mt-1 p-3 border border-gray-300 rounded-md "
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

            <div class="flex flex-col mb-6 relative item">
                    <router-link to="/reset-password" class="text-blue-500 p-2 rounded-md cursor-pointer">
                      Forgot password?
                    </router-link>
                  </div>
                  <button 
                        @click="handleLogin" 
                        :disabled="isLoading" 
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                      >
                        Log in
                    </button>
            </div>

    </div>

   <!-- OTP Verification Form -->
      <div v-else class="flex flex-col p-9 gap-4 bg-white w-full max-w-md h-auto rounded-lg shadow-lg sm:mx-4 md:mx-8 lg:mx-16 xl:mx-32">
        <div class="flex justify-center mb-6">
          <img src="@/assets/EqualynxLogo.png" alt="Equalynx Logo" class="w-32 h-32 object-contain" />
        </div>
        <div class="flex flex-col mb-4"> 
          <input 
            v-model="otp" 
            placeholder="Enter OTP" 
            class="mt-1 p-3 border border-gray-300 rounded-md "
          />
        </div>
        <button 
          @click="verifyOtpAction" 
          :disabled="isLoading" 
          class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
        >
          Verify OTP
        </button>
        <!-- Resend OTP Button -->
        <div class="flex flex-col mb-4">
    <!-- OTP Resend Button -->
          <button
            @click="resendOtpAction"
            :disabled="isResendDisabled"
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
          >
            Resend OTP
          </button>
          <div v-if="isResendDisabled" class="text-red-500 mt-2">
            Please wait {{ timer }} seconds before resending.
          </div>
        </div>
      </div>
  </div>
</template>

<script>
import Swal from 'sweetalert2';
import { mapActions } from 'vuex';
import backgroundImage from '@/assets/dataCenter.jpg';
export default {
  name: 'LoginPage',
  data() {
    return {
      backgroundImage,
      email: '',
      password: '',
      otp: '',
      showPassword: false,
      isLoading: false,
      otpVerified: false, // Flag to show OTP verification or login
      isResendDisabled: false,
      timer: 0,
      otpCooldownTime: 30, // In seconds (change this to your desired cooldown)
    };
  },
  methods: {
    ...mapActions('auth', ['login', 'verifyOtp']), // Map the actions from Vuex

    // Login Action
    async handleLogin() {
      this.isLoading = true; // Show loading state

      Swal.fire({
        title: 'Logging in...',
        text: 'Please wait while we process your login.',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      try {
        // Call the login action
        console.log("Email entered:", this.email); // Debugging line
        const response = await this.login({ email: this.email, password: this.password });

        // If login is successful, show OTP verification form
        if (response.success) {
          Swal.close();
          this.isLoading = false;
          this.otpVerified = true; // Show OTP input form

          // ✅ Store email for later use in verifyOtp
          localStorage.setItem('userEmail', this.email);
        } else {
          throw new Error(response.message);
        }
      } catch (error) {
        Swal.close();
        this.isLoading = false;
        Swal.fire({
          title: 'Login Failed!',
          text: error.message || 'Invalid credentials. Please try again.',
          icon: 'error',
          confirmButtonText: 'OK',
        });
      }
    },

    async verifyOtpAction() {
      this.isLoading = true; // Show loading state

      Swal.fire({
        title: 'Verifying OTP...',
        text: 'Please wait while we verify your OTP.',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      try {
        const email = localStorage.getItem('userEmail');
        if (!email) {
          console.error("Email not found in localStorage");
          return; // Handle this case (e.g., show error, prevent API call, etc.)
        }

        // Call the verifyOtp action and store the response
        const response = await this.$store.dispatch('auth/verifyOtp', { email, otp: this.otp });


        if (response.success) {
          Swal.close();
          this.isLoading = false;

          // Navigate to appropriate page based on user role
          if (response.userRole === 'admin') {
            this.$router.push("/UserManagement");
          } else if (response.userRole === 'manager') {
            this.$router.push("/AdminTransaction");
          } else if (response.userRole === 'warehouse_staff') {
            this.$router.push("/AdminInventory");
          } else if (response.userRole === 'procurement') {
            this.$router.push("/procurement");
          } else {
            this.$router.push("/"); // Default fallback
          }
        } else {
          throw new Error(response.message);
        }
      } catch (error) {
        Swal.close();
        this.isLoading = false;
        Swal.fire({
          title: 'OTP Verification Failed!',
          text: error.message || 'Invalid OTP. Please try again.',
          icon: 'error',
          confirmButtonText: 'OK',
        });
      }
    },

    async resendOtpAction() {
      // Disable the button and start the timer
      this.isResendDisabled = true;
      this.timer = this.otpCooldownTime;

      // Start a countdown
      const interval = setInterval(() => {
        this.timer--;
        if (this.timer <= 0) {
          clearInterval(interval);
          this.isResendDisabled = false; // Enable the button after cooldown
        }
      }, 1000);

      try {
        const response = await this.$store.dispatch('auth/resendOtp', { email: this.email });
        console.log(response.message);
      } catch (error) {
        console.error("Failed to resend OTP:", error);
      }
    },
    togglePasswordVisibility() {
      this.showPassword = !this.showPassword;
    }

  },
};
</script>