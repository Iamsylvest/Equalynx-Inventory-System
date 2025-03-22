<template>
   
    <div class="p-8 dark:text-custom-white ">
     <header class="flex flex-row justify-between items-center">
         <h1 class="text-2xl whitespace-nowrap">Archived</h1>
         <div class="hidden sm:flex md:flex lg:flex items-end justify-end ">
             <Notification />
             <Profile />
         </div>
     </header>
 
         <div>
             <archivedDr v-if="userRole === 'procurement' || userRole === 'manager'"  />
             <archiveRr v-if="userRole === 'warehouse_staff'"  />
         </div>
 
 </div>
 
 
 </template>

<script>
import { mapGetters } from 'vuex';
import Profile from '@/components/admin/Notification/Profile.vue';
import Notification from '@/components/admin/Notification/Notification.vue';
import archivedDr from '@/components/admin/Transaction/archivedDr.vue';
import archiveRr from '@/components/admin/Transaction/archiveRr.vue';
export default {
    components :{
        Notification,
        Profile,
        archivedDr,
        archiveRr,

 
    },
    computed:{
        ...mapGetters('auth', ['user', 'userRole']),

    },
      




};
</script>
