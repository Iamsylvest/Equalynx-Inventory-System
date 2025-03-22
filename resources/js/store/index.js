import { createStore } from 'vuex';
import createPersistedState from 'vuex-persistedstate';
import auth from './modules/auth';
import darkmode from './modules/darkmode'; // Import the darkmode module


export default createStore({
  modules: {
    auth,
    darkmode, // ADD DARKMODE MODULE HERE
  },
  plugins: [
    createPersistedState({
      paths: ['auth', 'darkmode'], // Specify which modules to persist // persists both auth and darkmode modules
    }),
  ],
});