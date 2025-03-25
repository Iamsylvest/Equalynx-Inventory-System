<template>
   
    <div class="p-8 dark:text-custom-white">
     <header class="flex flex-row justify-between items-center">
         <h1 class="text-2xl whitespace-nowrap">Dashboard</h1>
         <div class="hidden sm:flex md:flex lg:flex items-end justify-end">
             <Notification />
             <Profile />
         </div>
     </header>
 
         <div>
             <Procurementdashboard />
         </div>
 
 </div>
 
 
 </template>

<script>
import { mapGetters } from 'vuex';
import Profile from '@/components/admin/Notification/Profile.vue';
import Notification from '@/components/admin/Notification/Notification.vue';
import Procurementdashboard from '@/components/admin/Procurement/Procurementdashboard.vue';

export default {
    components :{
        Procurementdashboard,
        Profile,
        Notification,

 
    },
    computed:{
        ...mapGetters(['isAuthenticated']), // Map the Vuex getter

    },

};
</script>
