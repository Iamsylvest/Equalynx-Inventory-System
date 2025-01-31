<template>
  <div class="flex h-screen">
    <!-- Show Navbar only if the user is authenticated and not on login/reset-password pages -->
    <Navbar v-if="showNavbar" />

    <!-- Render the rest of the application (views) -->
    <div class="flex-1 overflow-auto">
      <router-view />
    </div> 
  </div>
</template>

<script>
import { computed } from "vue";
import { useStore } from "vuex";
import { useRoute } from "vue-router";
import Navbar from "./components/Navbar.vue";

export default {
  components: {
    Navbar,
  },

  setup() {
    const store = useStore();
    const route = useRoute();

    // Hide navbar on login and reset password pages
    const showNavbar = computed(() => {
      return store.getters["auth/isAuthenticated"] && !["/login", "/reset-password"].includes(route.path);
    });

    

    return { showNavbar };
  },
};
</script>