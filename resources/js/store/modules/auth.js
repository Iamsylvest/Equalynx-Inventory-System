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

        // Save token in Vuex state and localStorage
        commit('setToken', token);
        localStorage.setItem('authToken', token);

        // Fetch and save user details
        const userResponse = await axios.get('/api/user', {
          headers: { Authorization: `Bearer ${token}` },
        });
        commit('setUser', userResponse.data);

        localStorage.setItem('authUser', JSON.stringify(userResponse.data));

        return true; // Indicate successful login
      } catch (error) {
        console.error('Login failed:', error);
        throw new Error(error.response?.data?.message || 'Login failed');
      }
    },
    restoreSession({ commit }) {
      // Restore token and user from localStorage
      const token = localStorage.getItem('authToken');
      const user = JSON.parse(localStorage.getItem('authUser'));

      if (token && user) {
        commit('setToken', token);
        commit('setUser', user);
      }
    },
    logout({ commit }) {
      // Clear Vuex state and localStorage
      commit('logout');
      localStorage.removeItem('authToken');
      localStorage.removeItem('authUser');
    },
  },
  getters: {
    isAuthenticated: (state) => !!state.token,
    user: (state) => state.user,
    userRole: (state) => state.user?.role || 'guest', // Optional user role getter
  },
};