import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
export default {
  namespaced: true,
  state: {
    token: null,
    user: null,
  },
  mutations: {
    setToken(state, token) {
      state.token = token;
    },
    setUser(state, user) {
      state.user = user;
      console.log("User set in Vuex:", user); // Debugging log
      console.log("Setting user in Vuex:", user); // Debugging
    },
    logout(state) {
      state.token = null;
      state.user = null;
    },
  },
  actions: {
    async login({ commit }, credentials) {
      try {
        const response = await axios.post('/api/login', credentials);
        const user = response.data.user;
    
        commit('setUser', user); // Just save the user if needed
        // ✅ Store email in localStorage for OTP verification
        localStorage.setItem('userEmail', user.email);
        localStorage.setItem('pendingUser', JSON.stringify(user)); // Optional

        return { message: 'OTP sent', success: true };
      } catch (error) {
        return { message: error.response?.data?.message || 'Login failed', success: false };
      }
    },

    async resendOtp({ commit }, { email }) {
      try {
        const response = await axios.post('/api/resend-otp', { email });
  
        if (response.status === 200) {
          return { success: true };
        } else {
          return { success: false, message: response.data.message };
        }
      } catch (error) {
        console.error(error);
        return { success: false, message: error.response?.data?.message || 'An error occurred.' };
      }
    },
    
   async verifyOtp({ commit }, { email, otp }) {
        try {
          // Making API call to verify OTP
          console.log('Sending OTP payload:', { email, otp });
          const response = await axios.post('/api/verify-otp', { email, otp });

          // Assuming the backend sends back 'token' and 'user' on successful verification
          const token = response.data.token;
          const user = response.data.user;

          // Commit token and user to Vuex store
          commit('setToken', token);
          commit('setUser', user);

          // Storing token and user data in localStorage for persistence
          localStorage.setItem('authToken', token);
          localStorage.setItem('authUser', JSON.stringify(user));

          // Setting Axios default header for Authorization
          axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

          // Initialize Echo for real-time broadcasting (if needed)
          if (window.Echo) {
            window.Echo.disconnect(); // Clean up previous Echo instance
          }

          window.Echo = new Echo({
            broadcaster: 'pusher',
            key: import.meta.env.VITE_PUSHER_APP_KEY,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
            encrypted: true,
            forceTLS: true,
            authEndpoint: '/broadcasting/auth',
            auth: {
              headers: {
                Authorization: `Bearer ${token}`,
              },
            },
          });

          // Return success message and user role information (if any)
          return { message: 'OTP verification successful!', success: true, userRole: user.role };

        } catch (error) {
          // Return a friendly error message in case of failure
          return {
            message: error.response?.data?.message || 'OTP verification failed. Please try again.',
            success: false,
          };
        }
      },

    restoreSession({ commit }) {
      const token = localStorage.getItem('authToken');
      let user = null;

      try {
        user = JSON.parse(localStorage.getItem('authUser'));
      } catch (error) {
        console.error("Failed to parse user data from localStorage:", error);
      }

      if (token && user) {
        commit('setToken', token);
        commit('setUser', user);
      } else {
        commit('logout');
      }
    },
    async logout({ commit }) {
      try {
          // Send request to invalidate token on Laravel
          await axios.post('/api/logout', {}, {
              headers: {
                  Authorization: `Bearer ${localStorage.getItem("authToken")}`
              }
          });
      } catch (error) {
          console.error("Logout API error:", error);
      }
      
        // ✅ Disconnect Pusher on logout
        if (window.Echo) {
          window.Echo.disconnect();
          window.Echo = null;
          console.log('Pusher connection disconnected');
      }
  
      // Clear token and user from Vuex and local storage
      commit("setToken", null);
      commit("setUser", null);
      localStorage.removeItem("authToken");
      localStorage.removeItem("authUser");
      localStorage.removeItem("userEmail"); // <- Important for OTP-based login
      localStorage.removeItem("otp");       // <- If you're storing OTP temporarily
  
      // Clear Laravel session cookies manually (important for session-based auth)
      document.cookie = "XSRF-TOKEN=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
      document.cookie = "laravel_session=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
  
      // Reload page to clear cached state
      setTimeout(() => {
          window.location.href = "/login";
      }, 100);
  }
  },
  getters: {
    isAuthenticated: (state) => !!state.token,
    user: (state) => state.user,
    userRole: (state) => (state.user ? state.user.role : ''),
  },
};