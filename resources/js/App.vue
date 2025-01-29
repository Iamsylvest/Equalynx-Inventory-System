<template>
  <div class=" flex h-screen">
    <!-- Show Navbar only if the user is authenticated -->
    <Navbar v-if="isAuthenticated && isAdminNavbarVisbile" />

    <!-- Render the rest of the application (views) -->
   <div class="flex-1 overflow-auto"  >
    <router-view />
   </div> 
  
  </div>
</template>

<script>
import { mapGetters } from 'vuex'; // To map the Vuex getter
import Navbar from './components/Navbar.vue'; // Correct casing here

export default {
  name: 'App',

  components: {
    Navbar,
  },
  data() {
      return{
        isAdminNavbarVisbile: true,
      }
  },
  methods: {
    toggleNavbar(){
      this.isAdminNavbarVisbile =! this.isAdminNavbarVisbile
    },
  },

  computed: {
    // Map the isAuthenticated getter from Vuex store
    ...mapGetters('auth', ['isAuthenticated']),
  },
  created() {
    // Attempt to restore the session from localStorage
    this.$store.dispatch('auth/restoreSession');
  }
};
</script>