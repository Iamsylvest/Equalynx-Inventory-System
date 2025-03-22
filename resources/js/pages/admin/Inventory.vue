<template>
   
    <div class="p-8 dark:text-custom-white ">
     <header class="flex flex-row justify-between items-center">
         <h1 class="text-2xl whitespace-nowrap">Inventory</h1>
         <div class="hidden sm:flex md:flex lg:flex items-end justify-end ">
             <Notification />
             <Profile />
         </div>
     </header>
 
         <div>
             <InventoryTable />
         </div>
 
 </div>
 
 
 </template>

<script>
import { mapGetters } from 'vuex';
import Profile from '@/components/admin/Notification/Profile.vue';
import Notification from '@/components/admin/Notification/Notification.vue';
import InventoryTable from '@/components/admin/Inventory/InventoryTable.vue';

export default {
    components :{
        InventoryTable,
        Profile,
        Notification,

 
    },
    computed:{
        ...mapGetters(['isAuthenticated']), // Map the Vuex getter

    },

};
</script>
