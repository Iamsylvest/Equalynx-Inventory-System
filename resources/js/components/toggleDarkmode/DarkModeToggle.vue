<template>
  <div >
    <div class=" flex flex-row ">
      <div class=" flex flex-row">
        <p class="text-lg whitespace-nowrap p-2">Dark Mode</p>
        <button @click="toggleDarkMode" class=" text-sm toggle-btn font-roboto bg-custom-blue text-custom-white dark:bg-custom-table cursor:pointer px-5 border border-black dark:border-white rounded-xl ">
          {{ isDarkMode ? ' Off' : ' On' }}
        </button>
      </div>
   </div>
   <div>

 

   </div>
  </div>
  </template>
  
  <script>
  import { computed } from 'vue';
  import { useStore } from 'vuex';
  
  export default {
    setup() {
      const store = useStore();
  
      // Access dark mode state from Vuex
      const isDarkMode = computed(() => store.getters['darkmode/isDarkMode']);
  
      // Dispatch action to toggle dark mode
      const toggleDarkMode = () => {
        store.dispatch('darkmode/toggleDarkMode');
      };
  
      return { isDarkMode, toggleDarkMode };
    },
  };
  </script>
