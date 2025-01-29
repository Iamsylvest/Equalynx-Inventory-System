import { createApp } from 'vue';
import router from './router';  // Make sure router is correctly imported
import App from './App.vue';    // Make sure this file exists or change it accordingly
import store from './store';    // This assumes 'store' is in 'resources/js/store'

const app = createApp(App);

// Restore session before mounting the app
store.dispatch('auth/restoreSession')
  .then(() => {
    // Register Vue Router and Vuex store only after restoring session
    app.use(router);  // Register Vue Router
    app.use(store);    // Register Vuex store

    app.mount('#app'); // Mount the app to the div with id "app"
  })
  .catch(error => {
    console.error('Failed to restore session:', error);
    app.mount('#app'); // Mount the app even if session restoration fails
  });