<template>
  <div class="flex h-screen  ">
    <!-- Show Navbar only if the user is authenticated and not on login/reset-password pages -->
    <Navbar v-if="showNavbar" class="dark:bg-custom-table" />
  
    <!-- Render the rest of the application (views) -->
    <div class="flex-1 overflow-auto  ">

      <router-view  />
    </div>
  </div>
</template>

<script>
import { computed, watch } from "vue";
import { useStore } from "vuex";
import { useRoute } from "vue-router";
import Navbar from "@/components/Navbar.vue";

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

    // Access dark mode state from Vuex
    const isDarkMode = computed(() => store.getters["darkmode/isDarkMode"]);

    // ✅ Watch for dark mode changes and update HTML class
    watch(isDarkMode, (value) => {
      if (value) {
        document.documentElement.classList.add('dark'); // ✅ Use html instead of body
      } else {
        document.documentElement.classList.remove('dark');
      }
    });

    // ✅ Set initial state on component mount
    if (isDarkMode.value) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }

    return { showNavbar, isDarkMode };
  },
};
</script>
