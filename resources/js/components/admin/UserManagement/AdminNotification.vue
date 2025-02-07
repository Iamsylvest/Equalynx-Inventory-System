<template>
    <div  class="font-roboto">
      <!-- Modal Button -->
      <button
        @click="openModal"
        :class="{
          'bg-custom-blue': isClicked,
          'hover:bg-custom-blue ': !isClicked,
          'text-white': isClicked,
          'hover:text-white': !isClicked,
          'active:bg-custom-blue': isClicked
  
        }"
        class="flex items-center justify-center w-12 h-12 cursor-pointer transition duration-300 focus:outline-none rounded-full"
      >
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
        <path fillRule="evenodd" d="M5.25 9a6.75 6.75 0 0 1 13.5 0v.75c0 2.123.8 4.057 2.118 5.52a.75.75 0 0 1-.297 1.206c-1.544.57-3.16.99-4.831 1.243a3.75 3.75 0 1 1-7.48 0 24.585 24.585 0 0 1-4.831-1.244.75.75 0 0 1-.298-1.205A8.217 8.217 0 0 0 5.25 9.75V9Zm4.502 8.9a2.25 2.25 0 1 0 4.496 0 25.057 25.057 0 0 1-4.496 0Z" clipRule="evenodd" />
      </svg>
  
      </button>
  
      <!-- Modal -->
      <div v-if="modalNotification" class="absolute inset-0 flex items-center justify-end bg-opacity-50">
        <!-- Background Overlay: When clicked, closes the modal -->
        <div @click="closeModal" class="fixed inset-0 z-10 opacity-50"></div>
  
        <!-- Modal Content -->
        <div
          @click.stop
          class="bg-white border-1 shadow-lg rounded-md px-4 py-10 z-20 mx-4 w-full max-w-sm h-[550px] relative left-[-20px] top-5  min-w-[200px] max-h-[700px] min-h-[300px]"
        >
          <!-- Notif Content -->
          <div class="relative top-[-30px] space-y-6">
            <div class="flex justify-between">
              <h1 class="font-bold text-2xl">Notification</h1>
              <p class="relative top-1 cursor-pointer hover:text-blue-500">See all</p>
            </div>
  
            <!-- Individual Item -->
            <div class="flex items-center space-x-4 cursor-pointer rounded-full hover:border-4 hover:bg-gray-200">
              <img src="@/assets/Sylvest.jpg" alt="Description" class="object-cover w-20 h-20 rounded-full border-4 border-gray-300 shadow-lg" />
              <div class="flex-1">
                <p>
                  <span class="font-bold">Sylvest Madelo</span> <span>created a new user.</span>
                </p>
                <p class="text-blue-400 text-sm mt-1">10h</p>
              </div>
            </div>
  
            <!-- More Items -->
            <div class="flex items-center space-x-4 cursor-pointer rounded-full hover:border-4 hover:bg-gray-200">
              <img src="@/assets/sakura.jpg" alt="Description" class="object-cover w-20 h-20 rounded-full border-4 border-gray-300 shadow-lg" />
              <div class="flex-1">
                <p>
                  <span class="font-bold">SAKURA</span> <span>created a new user.</span>
                </p>
                <p class="text-blue-400 text-sm mt-1">2h</p>
              </div>
            </div>
  
            <!-- More Items -->
            <div class="flex items-center space-x-4 cursor-pointer rounded-full hover:border-4 hover:bg-gray-200">
              <img src="@/assets/initial.jpg" alt="Description" class="object-cover w-20 h-20 rounded-full border-4 border-gray-300 shadow-lg" />
              <div class="flex-1">
                <p>
                  <span class="font-bold">Takumi Fujiwara</span> <span>created a new user.</span>
                </p>
                <p class="text-blue-400 text-sm mt-1">5h</p>
              </div>
            </div>
  
            <!-- More Items -->
            <div class="flex items-center space-x-4 cursor-pointer rounded-full hover:border-4 hover:bg-gray-200">
              <img src="@/assets/frieren.jpg" alt="Description" class="object-cover w-20 h-20 rounded-full border-4 border-gray-300 shadow-lg" />
              <div class="flex-1">
                <p>
                  <span class="font-bold">Frieren</span> <span>created a new user.</span>
                </p>
                <p class="text-blue-400 text-sm mt-1">10h</p>
              </div>
            </div>
  
            <button class="bg-gray-400 flex items-center justify-center p-2 w-full border-1 rounded-lg text-white hover:bg-gray-500">
              <p>See previous notifications</p>
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        modalNotification: false,
        isClicked: false, // to track if the button was clicked
      };
    },
    methods: {
      openModal() {
        this.modalNotification = true;
        this.isClicked = true;
      },
      closeModal() {
        this.modalNotification = false;
        this.isClicked = false;
      },
    },
  };
  </script>