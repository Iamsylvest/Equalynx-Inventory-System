<template>
   
    <div class="p-8 ">
     <header class="flex flex-row justify-between items-center">
         <h1 class="text-2xl  whitespace-nowrap">Notification</h1>
         <div class="hidden sm:flex md:flex lg:flexitems-end justify-end">
           <Notification/>
           <Profile/>
         </div>
     </header>
 
        <div class="w-auto h-[500px] bg-white drop-shadow-md mt-10">
            <div class="mx-5 py-5 w-auto h-full">
              <NotificationPage/>
            </div>

            
        </div>
    </div>

 
 
 </template>
 

<script>

import { mapGetters } from 'vuex'; // ✅ Import mapGetters
import Notification from '@/components/admin/Notification/Notification.vue';
import NotificationPage from '@/components/admin/Notification/NotificationPage.vue';
import Profile from '@/components/admin/Notification/Profile.vue';


export default {

    components: {
        Notification,
        Profile,
        NotificationPage,
    },
    data () {
            return {
               showNotif: 'Today',
            };
        },
    computed: {
      ...mapGetters(['isAuthenticated']) // Map the Vuex getter
    },
    

}
    
</script>