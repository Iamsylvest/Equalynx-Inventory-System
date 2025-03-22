<template>
   
    <div class="p-8 dark:text-custom-white ">
     <header class="flex flex-row justify-between items-center">
         <h1 class="text-2xl  whitespace-nowrap">Settings</h1>
         <div class="flex items-end justify-end">
            <Notification/>
            <Profile/>
         </div>
     </header>
     <div class=" flex flex-col space-y-5 font-roboto p-8 mt-16 h-100 border  shadow-lg  dark:border-none rounded-lg dark:bg-custom-table">
     
        <DarkModeToggle />
        <changePassword />

        <thresholdUpdate v-if="userRole === 'admin'" />
     </div>
 

            
 
 </div>
 
 

 </template>
 
<script>
import { mapGetters} from "vuex";
import Notification from '@/components/admin/Notification/Notification.vue';
import Profile from '@/components/admin/Notification/Profile.vue';
import DarkModeToggle from '@/components/toggleDarkmode/DarkModeToggle.vue';
import changePassword from '@/auth/changePassword.vue';
import thresholdUpdate from '@/components/Settings/thresholdUpdate.vue';

export default {
    components:
    {
        Notification,
        Profile,
        DarkModeToggle,
        changePassword,
        thresholdUpdate,
      
    },
    computed:{
        ...mapGetters( 'auth',['user', 'userRole']),
    }

}
    
</script>