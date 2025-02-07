import axios from 'axios';

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
        const token = response.data.token;
        const user = response.data.user;

        commit('setUser', user); // Store user with role
        commit('setToken', token); // Save token in Vuex state and localStorage
        localStorage.setItem('authToken', token);
        localStorage.setItem('authUser', JSON.stringify(user));

        const userResponse = await axios.get('/api/user', {
          headers: { Authorization: `Bearer ${token}` },
        });

        commit('setUser', userResponse.data);
        localStorage.setItem('authUser', JSON.stringify(userResponse.data));

           // Return user role for redirection
        return user.role;  // ✅ FIXED: Now it returns the role

      } catch (error) {
        console.error('Login failed:', error);
        throw new Error(error.response?.data?.message || 'Login failed');
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
    logout({ commit }) {
      commit("setToken", null);
      commit("setUser", null);
      localStorage.removeItem("authToken");
      localStorage.removeItem("authUser");

      // Force reload to clear cached state
      setTimeout(() => {
        window.location.href = "/login";
      }, 100);
    }
  },
  getters: {
    isAuthenticated: (state) => !!state.token,
    user: (state) => state.user,
    userRole: (state) => state.user ? state.user.data.role : '',
  },
};