<template>
    <div class="relative flex h-screen font-roboto">
     <!-- Sidebar -->
     <div
     v-bind:style="{ width: isSidebarWide ? '220px' : '100px' }"
     class="flex flex-col items-center justify-between shadow h-full "
   >
     <div class="relative dark:text-white">
  
             <!-- Logo -->
               <div class="flex justify-center py-5">
               <img
               src="@/assets/EqualynxLogo.png"
               alt="Equalynx Logo"
               class="w-full h-32 object-contain"
               />
               </div>

         <router-link
           v-if="userRole === 'procurement'"
           to="/procurement" class="flex items-center mb-2  lg:px-8 p-4 text-md  cursor-pointer hover:text-white-600 hover:bg-custom-blue  hover:text-white focus:outline-none"
           @click="activeLink = ('Procurement')"
                   :class="{
                      'bg-custom-blue text-white dark:bg-custom-main': activeLink === 'Procurement',
                      'hover:bg-custom-blue hover:text-white hover:dark:bg-custom-main': activeLink !== 'Procurement'
                   }"
                 >
                 <svg 
                 xmlns="http://www.w3.org/2000/svg" 
                 fill="none" 
                 viewBox="0 0 24 24" 
                 stroke-width="1.5" 
                 stroke="currentColor" 
                 class="size-6 ml-6 lg:ml-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                  </svg>

               <span v-if="isSidebarWide" class="ml-2 text-[12px]">Procurement</span> <!-- Only visible when wide -->
           </router-link>

           
               
               <router-link v-if="userRole === 'admin'"
                   to="/UserManagement"
                   class="flex items-center mb-2 lg:px-8 p-4 text-md cursor-pointer hover:text-white hover:bg-custom-blue focus:outline-none"
                   @click="activeLink = ('UserManagement')"
                   :class="{
                     'bg-custom-blue text-white dark:bg-custom-main': activeLink === 'UserManagement',
                     'hover:bg-custom-blue hover:text-white hover:dark:bg-custom-main': activeLink !== 'UserManagement',
            
                   }"
                 >
                   <svg
                     xmlns="http://www.w3.org/2000/svg"
                     class="size-6 ml-6 lg:ml-2"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="1.5"
                     stroke="currentColor"
                   >
                     <path
                       stroke-linecap="round"
                       stroke-linejoin="round"
                       d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"
                     />
                   </svg>

                   <span v-if="isSidebarWide" class="ml-2 text-[12px]">User Management</span>
                 </router-link>
  
           <!-- Repeat for other links -->
  
           <router-link 
           v-if="userRole === 'admin' || userRole === 'manager' || userRole === 'warehouse_staff' || userRole === 'procurement'"
           to="/AdminInventory" class="flex items-center mb-2  lg:px-8 p-4 text-md  cursor-pointer hover:text-white-600 hover:bg-custom-blue  hover:text-white focus:outline-none"
                   @click="activeLink = ('Inventory')"
                   :class="{
                     'bg-custom-blue text-white  dark:bg-custom-main': activeLink === 'Inventory',
                     'hover:bg-custom-blue hover:text-white  hover:dark:bg-custom-main': activeLink !== 'Inventory',
                 
                   }"
                 >
               <svg 
                   xmlns="http://www.w3.org/2000/svg" 
                   fill="none" 
                   viewBox="0 0 24 24" 
                   stroke-width="1.5" 
                   stroke="currentColor" 
                   class="size-6 ml-6 lg:ml-2">
               <path 
                   stroke-linecap="round" 
                   stroke-linejoin="round" 
                   d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1m1.5.5-1.5-.5M6.75 7.364V3h-3v18m3-13.636 10.5-3.819" />
               </svg>
               <span v-if="isSidebarWide" class="ml-2 text-[12px]">Inventory</span> <!-- Only visible when wide -->
  
           </router-link>
  
             <router-link
             v-if="userRole === 'admin' || userRole === 'manager' || userRole === 'warehouse_staff' || userRole === 'procurement'"
             to="/AdminTransaction" class="flex items-center mb-2  lg:px-8 p-4 text-md cursor-pointer hover:text-white-600 hover:bg-custom-blue  hover:text-white focus:outline-none"
             @click="activeLink = ('Transaction')"
                   :class="{
                     'bg-custom-blue text-white  dark:bg-custom-main': activeLink === 'Transaction',
                     'hover:bg-custom-blue hover:text-white  hover:dark:bg-custom-main': activeLink !== 'Transaction'
                   }"
                 >
               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 ml-6 lg:ml-2">
                 <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
               </svg>
  
                 <span v-if="isSidebarWide" class="ml-2 text-[12px]">Transaction</span> <!-- Only visible when wide -->
  
             </router-link>
  
             <router-link 
             v-if="userRole === 'admin'"
             to="/ActivityLogs" class="flex items-center mb-2  lg:px-8 p-4 text-md cursor-pointer hover:text-white-600 hover:bg-custom-blue  hover:text-white focus:outline-none"
                  @click="activeLink = ('Activity')"
                   :class="{
                     'bg-custom-blue text-white  dark:bg-custom-main': activeLink === 'Activity',
                     'hover:bg-custom-blue hover:text-white  hover:dark:bg-custom-main': activeLink !== 'Activity'
                   }"
                 >
               <svg 
                   xmlns="http://www.w3.org/2000/svg" 
                   fill="none" 
                   viewBox="0 0 24 24" 
                   stroke-width="1.5" 
                   stroke="currentColor" 
                   class="size-6 ml-6 lg:ml-2">
               <path 
                   stroke-linecap="round" 
                   stroke-linejoin="round" 
                   d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
               </svg>
               <span v-if="isSidebarWide" class="ml-2 text-[12px]">Activity Logs</span> <!-- Only visible when wide -->
  
           </router-link>

           <router-link 
             v-if="userRole === 'admin'"
             to="/TransactionTrail" class="flex items-center mb-2  lg:px-8 p-4 text-md cursor-pointer hover:text-white-600 hover:bg-custom-blue  hover:text-white focus:outline-none"
                  @click="activeLink = ('Trail')"
                   :class="{
                     'bg-custom-blue text-white  dark:bg-custom-main': activeLink === 'Trail',
                     'hover:bg-custom-blue hover:text-white  hover:dark:bg-custom-main': activeLink !== 'Trail'
                   }"
                 >
                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 ml-6 lg:ml-2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" />
                </svg>
               <span v-if="isSidebarWide" class="ml-2 text-[12px]">Transaction Trail</span> <!-- Only visible when wide -->
  
           </router-link>
  
           <router-link
           v-if="userRole === 'admin' || userRole === 'procurement' || userRole === 'warehouse_staff' || userRole === 'manager'"
           to="/Notification" class="flex items-center mb-2  lg:px-8 p-4 text-md  cursor-pointer hover:text-white-600 hover:bg-custom-blue  hover:text-white focus:outline-none"
           @click="activeLink = ('Notification')"
                   :class="{
                     'bg-custom-blue text-white  dark:bg-custom-main': activeLink === 'Notification',
                     'hover:bg-custom-blue hover:text-white  hover:dark:bg-custom-main': activeLink !== 'Notification'
                   }"
                 >
               <svg 
                   xmlns="http://www.w3.org/2000/svg" 
                   fill="none" 
                   viewBox="0 0 24 24" 
                   stroke-width="1.5" 
                   stroke="currentColor" 
                   class="size-6 ml-6 lg:ml-2">
               <path 
                   stroke-linecap="round" 
                   stroke-linejoin="round" 
                   d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
               </svg>
               <span v-if="isSidebarWide" class="ml-2 text-[12px]">Notification</span> <!-- Only visible when wide -->
  
           </router-link>


           <router-link
           v-if="userRole === 'procurement' || userRole === 'warehouse_staff'"
           to="/Archived" class="flex items-center mb-2  lg:px-8 p-4 text-md  cursor-pointer hover:text-white-600 hover:bg-custom-blue  hover:text-white focus:outline-none"
           @click="activeLink = ('Archived')"
                   :class="{
                     'bg-custom-blue text-white  dark:bg-custom-main': activeLink === 'Archived',
                     'hover:bg-custom-blue hover:text-white  hover:dark:bg-custom-main': activeLink !== 'Archived'
                   }"
                 >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"    class="size-6 ml-6 lg:ml-2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                </svg>

               <span v-if="isSidebarWide" class="ml-2 text-[12px]">Archived</span> <!-- Only visible when wide -->
           </router-link>
  
           <router-link
           v-if="userRole === 'admin' || userRole === 'manager' || userRole === 'warehouse_staff' || userRole === 'procurement'"
           to="/Settings" class="flex items-center mb-2  lg:px-8 p-4 text-md  cursor-pointer hover:text-white-600 hover:bg-custom-blue  hover:text-white focus:outline-none"
           @click="activeLink = ('Settings')"
                   :class="{
                     'bg-custom-blue text-white  dark:bg-custom-main': activeLink === 'Settings',
                     'hover:bg-custom-blue hover:text-white  hover:dark:bg-custom-main': activeLink !== 'Settings'
                   }"
                 >
               <svg 
                   xmlns="http://www.w3.org/2000/svg" 
                   fill="none" 
                   viewBox="0 0 24 24" 
                   stroke-width="1.5" 
                   stroke="currentColor" 
                   class="size-6 ml-6 lg:ml-2">
               <path 
                   stroke-linecap="round" 
                   stroke-linejoin="round" 
                   d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
               <path 
                   stroke-linecap="round" 
                   stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
               </svg>
               <span v-if="isSidebarWide" class="ml-2 text-[12px]">Settings</span> <!-- Only visible when wide -->
           </router-link>

             


         </div>
  
       <header class="p-2 relative ">
         <!-- Fullscreen toggle button -->
         <button
           @click="wideSidebar"  
           class="bg-custom-blue text-white px-4 py-2 rounded flex items-center justify-center m-auto relative dark:bg-custom-table"
         >
           <svg
             xmlns="http://www.w3.org/2000/svg"
             fill="none"
             viewBox="0 0 24 24"
             stroke-width="1.5"
             stroke="currentColor"
             class="size-6"
           >
             <path
               stroke-linecap="round"
               stroke-linejoin="round"
               d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
             />
           </svg>
         </button>
  
       </header>
     </div>
   </div>
  </template>
<script>
import { mapGetters } from "vuex";

export default {
  data() {
    return {
      isSidebarWide: false, // Sidebar is closed by default
      activeLink: "",
    };
  },
  computed: {
    ...mapGetters("auth", ["user", "userRole"]), // Directly map userRole from Vuex getter
    
  },
  watch: {
    user(newUser) {
      if (newUser) {
        console.log("User changed:", newUser);
        this.$nextTick(() => {
          // Ensures the DOM is updated after user data is set
        });
      }
    },
  },
  mounted() {
    console.log("Mounted - User Role:", this.userRole);
  },

  methods: {
    wideSidebar() {
      this.isSidebarWide = !this.isSidebarWide;
    },
    setActiveLink(link) {
      this.activeLink = link;
    },
  },
};
</script>