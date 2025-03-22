export default {
    namespaced: true,
    state: {
      isDarkMode: false,
    },
    mutations: {
      toggleDarkMode(state) {
        state.isDarkMode = !state.isDarkMode;
        localStorage.setItem('isDarkMode', state.isDarkMode); // ✅ Save state to localStorage
      },
      setDarkMode(state, value) {
        state.isDarkMode = value;
      },
    },
    actions: {
      toggleDarkMode({ commit }) {
        commit('toggleDarkMode');
      },
      loadDarkMode({ commit }) {
        const savedMode = localStorage.getItem('isDarkMode') === 'true';
        commit('setDarkMode', savedMode);
      },
    },
    getters: {
      isDarkMode: (state) => state.isDarkMode,
    },
  };