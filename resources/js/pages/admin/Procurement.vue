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
             <ProcurementDashboard />
         </div>
 
 </div>
 
 
 </template>

<script>
import { mapGetters } from 'vuex';
import Profile from '@/components/admin/Notification/Profile.vue';
import Notification from '@/components/admin/Notification/Notification.vue';
import ProcurementDashboard from '@/components/admin/Procurement/ProcurementDashboard.vue';

export default {
    components :{
        ProcurementDashboard,
        Profile,
        Notification,

 
    },
    computed:{
        ...mapGetters(['isAuthenticated']), // Map the Vuex getter

    },

};
</script>
