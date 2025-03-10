import { createApp } from 'vue';
import router from './router';
import App from './App.vue';
import store from './store';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const app = createApp(App);

window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
  forceTLS: true,
  encrypted: true,
  authEndpoint: '/broadcasting/auth',
  auth: {
      headers: {
        Authorization: `Bearer ${store.state.auth.token}` // Get token from Vuex
      }
  },
  debug: true // Enable Echo debugging
});


// Listen for the popstate event (back button navigation)
window.addEventListener('popstate', () => {
  const currentPath = window.location.pathname;

  // If user is going back to the login page and is authenticated, log them out
  if (currentPath === '/login' && store.getters['auth/isAuthenticated']) {
    store.dispatch('auth/logout'); // This clears the session and token
  }
});

// Restore session before mounting the app
store.dispatch('auth/restoreSession')
  .then(() => {
    // Check if the user is authenticated before mounting
    if (store.getters['auth/isAuthenticated']) {
      // User is authenticated, do not show login page
      router.push('/'); // Redirect to home page if already logged in
    }

    app.use(router);  // Register Vue Router
    app.use(store);    // Register Vuex store
    app.mount('#app'); // Mount the app to the div with id "app"
  })
  .catch(error => {
    console.error('Failed to restore session:', error);
    // Optionally, show a login or error page if session restoration fails
    app.use(router);  // Register Vue Router even if session restoration fails
    app.use(store);    // Register Vuex store even if session restoration fails
    app.mount('#app'); // Mount the app regardless
  });