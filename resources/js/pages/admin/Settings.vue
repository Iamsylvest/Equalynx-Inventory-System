<template>
   
    <div class="p-8 ">
     <header class="flex flex-row justify-between items-center">
         <h1 class="text-2xl  whitespace-nowrap">Settings</h1>
         <div class="flex items-end justify-end">
            <Notification/>
            <Profile/>
         </div>
     </header>
 
     <SettingsForm/>
            
 
 </div>
 
 
 
 </template>
 
<script>
import Notification from '../../components/admin/Settings/Notification.vue';
import Profile from '../../components/admin/Settings/Profile.vue';
import SettingsForm from '../../components/admin/Settings/SettingsForm.vue';
export default {
    components:
    {
        Notification,
        Profile,
        SettingsForm,
    },

}
    
</script>