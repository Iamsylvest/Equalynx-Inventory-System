<template>
    <div class="">
      <!-- Search Input Field -->
      <div class="relative">
        <input
          v-model="query"
          type="text"
          placeholder="Search..."
          @keyup.enter="search"
          class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-200 drop-shadow-md w-full sm:w-60 md:w-43"
          aria-label="Search"
        />
        
        <!-- Search Icon (SVG) Inside Input -->
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="h-5 w-5 text-gray-500 absolute left-3 top-1/2 transform -translate-y-1/2"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"
          />
        </svg>
      </div>
    </div>
  </template>
  
  <script>
  export default {
  

    data() {
      return {
        query: "", // Model for the search query
      };
    },
    methods: {
      search() {
        if (this.query.trim()) {
          alert(`Searching for: ${this.query}`);
        } else {
          alert("Please enter a search query.");
        }
      },
    },
  };
  </script>