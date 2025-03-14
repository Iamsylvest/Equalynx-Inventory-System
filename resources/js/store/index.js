import { createStore } from 'vuex';
import createPersistedState from 'vuex-persistedstate';
import auth from './modules/auth';

export default createStore({
  modules: {
    auth,
  },
  plugins: [
    createPersistedState({
      paths: ['auth'], // Specify which modules to persist
    }),
  ],
});