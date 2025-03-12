<template>
   
    <div class="p-8 ">
     <header class="flex flex-row justify-between items-center">
         <h1 class="text-2xl  whitespace-nowrap">Settings</h1>
         <div class="flex items-end justify-end">
            <Notification/>
            <Profile/>
         </div>
     </header>
 

            
 
 </div>
 
 
 
 </template>
 
<script>
import Notification from '@/components/admin/Notification/Notification.vue';
import Profile from '@/components/admin/Notification/Profile.vue';

export default {
    components:
    {
        Notification,
        Profile,
      
    },

}
    
</script>