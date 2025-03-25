<template>
    <div>
      <!-- Profile Button -->
      <button
        @click="openModal"
        :class="{
         'bg-custom-blue dark:bg-custom-hover': isClicked,
        'hover:bg-custom-blue  hover:dark:bg-custom-hover ': !isClicked,
        'text-white': isClicked,
        'hover:text-white': !isClicked,
        'active:bg-custom-blue': isClicked
        }"
          class="relative flex items-center justify-center w-12 h-12 cursor-pointer transition duration-300 focus:outline-none rounded-full"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
          <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
        </svg>
      </button>
  
      <!-- Modal -->
      <div v-if="modalProfile"  class="absolute inset-0 flex items-center justify-end bg-opacity-50">
        <div @click="closeModal" class="fixed inset-0 z-10 opacity-50"></div>
        <div class="bg-white dark:bg-custom-table border-1 shadow-lg rounded-md px-4 py-10 z-20 mx-4 w-full max-w-sm h-[420px] absolute  top-20 right-12">
          <div class="flex items-center justify-end mb-4 relative top-[-30px]">
            <!-- Close Button -->
            <button @click="closeModal" class="flex items-center justify-center p-2 hover:bg-gray-100 rounded-full dark:hover:bg-custom-hover">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
  
          <!-- Profile Content -->
          <div class="flex flex-col items-center justify-center space-y-6 mt-16">
    
            <!-- Name and Role -->
            <div>
              <h1 class="font-bold relative top-[-50px] text-2xl">{{ userDetails.first_name}} {{ userDetails.middle_name ?? ''}} {{ userDetails.last_name}}</h1>
            </div>
            <div>
              <h1 class="font-bold relative top-[-55px] text-md">{{ userDetails.role ?? ''}}</h1>
            </div>
  
            <!-- Settings Button -->
            <div>
              <button>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 relative left-[60px] top-[-32px]">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <router-link 
                  to="/Settings">
                  <h1 class="font-bold relative top-[-75px] text-md border-t-2 p-4 px-[100px]">Settings</h1>
                  </router-link>
              </button>
            </div>
  
            <!-- Log Out Button -->
            <div>
              <button @click="logout">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 relative left-[60px] top-[-80px]">
                  <path fill-rule="evenodd" d="M16.5 3.75a1.5 1.5 0 0 1 1.5 1.5v13.5a1.5 1.5 0 0 1-1.5 1.5h-6a1.5 1.5 0 0 1-1.5-1.5V15a.75.75 0 0 0-1.5 0v3.75a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V5.25a3 3 0 0 0-3-3h-6a3 3 0 0 0-3 3V9A.75.75 0 1 0 9 9V5.25a1.5 1.5 0 0 1 1.5-1.5h6ZM5.78 8.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 0 0 0 1.06l3 3a.75.75 0 0 0 1.06-1.06l-1.72-1.72H15a.75.75 0 0 0 0-1.5H4.06l1.72-1.72a.75.75 0 0 0 0-1.06Z" clip-rule="evenodd" />
                </svg>
                <h1 class="font-bold relative top-[-123px] text-md border-t-2 p-4 px-[100px]">Log out</h1>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
import axios from 'axios';


  export default {
    data() {
      return {
        modalProfile: false,
        isClicked: false,
        userDetails:{},
      };
    },
    methods: {
      openModal() {
        this.modalProfile = true;
        this.isClicked = true;
      },
      closeModal() {
        this.modalProfile = false;
        this.isClicked = false;
      },
      logout() {
        // Call Vuex action to clear the session
        this.$store.dispatch('auth/logout');

        // Redirect the user to the login page
        this.$router.push('/login');

        // Close the modal if it's open
        this.closeModal();
      },
      async fetchUserDetails() {
        try {
          // ✅ Directly call the `/api/user` endpoint to get the logged-in user
          const response = await axios.get('/api/user');
          console.log('Fetch User Details', response.data);

          // ✅ Use nullish coalescing for fallback to an empty object
          this.userDetails = response.data.data ?? {}; 
        } catch (error) {
          console.error('Failed to Fetch User Details', error);
        }
      },
    },
    mounted(){
      this.fetchUserDetails();
      }
  };
</script>