<template>
   
    <div class="p-8 ">
     <header class="flex flex-row justify-between items-center">
         <h1 class="text-2xl  whitespace-nowrap">Notification</h1>
         <div class="hidden sm:flex md:flex lg:flexitems-end justify-end">
           <Notification/>
           <Profile/>
         </div>
     </header>
 
        <!-- <div 
            class="flex flex-row items-center justify-between p-4 space-x-6 border-b-2 border-gray">
                 First group of buttons (Today & Previous) 
                <div class="flex space-x-4">
                    <button 
                    @click="showNotif = 'Today'"
                    :class="{'text-blue-700 underline': showNotif === 'Today',
                            'hover:text-blue-700 hover:underline': showNotif === 'Today'
                }"
                    class="px-4 py-2 whitespace-nowrap hover:text-blue-700 hover:underline">Today</button>
                    <button 
                    :class="{'text-blue-700 underline': showNotif === 'Previous',
                                'hover:text-blue-700 hover:underline': showNotif === 'Previous'
                }"  
                    @click="showNotif = 'Previous'"
                    class="px-4 py-2 whitespace-nowrap hover:text-blue-700 hover:underline">Previous</button>
                </div>

                 Second group of buttons (Mark as read & Clear all) 
                <div class="flex space-x-4">
                    <button class="px-4 py-2 whitespace-nowrap hover:text-blue-700 hover:underline">Mark as read</button>
                    <button class="px-4 py-2 whitespace-nowrap hover:text-red-700 hover:underline">Clear all</button>
                </div>  
        </div> 

        <div class="mt-8">
            <Today v-if="showNotif === 'Today'" class="w-full sm:w-auto" />
            <Previous v-if="showNotif === 'Previous'" class="w-full sm:w-auto" />
        </div> -->
        <div class="w-auto h-[500px] bg-white drop-shadow-md mt-10">
            <div class="mx-5 py-5 w-auto h-full">
                <!-- First group of buttons (Today & Previous)  -->
                <div class="flex items-center justify-center md:justify-between">
                    <div class="flex space-x-4">
                    <button 
                    @click="showNotif = 'Today'"
                    :class="{'text-blue-700 underline': showNotif === 'Today',
                            'hover:text-blue-700 hover:underline': showNotif === 'Today'
                }"
                    class="px-4 py-2 whitespace-nowrap hover:text-blue-700 hover:underline">Today</button>
                    <button 
                    :class="{'text-blue-700 underline': showNotif === 'Previous',
                                'hover:text-blue-700 hover:underline': showNotif === 'Previous'
                }"  
                    @click="showNotif = 'Previous'"
                    class="px-4 py-2 whitespace-nowrap hover:text-blue-700 hover:underline">Previous</button>
                </div>

                 <!-- Second group of buttons (Mark as read & Clear all)  -->
                    <div class="hidden sm:flex md:flex space-x-4">
                        <button class="px-4 py-2 whitespace-nowrap hover:text-blue-700 hover:underline">Mark as read</button>
                        <button class="px-4 py-2 whitespace-nowrap hover:text-red-700 hover:underline">Clear all</button>
                    </div>  
                </div>
                <div class="flex space-x-4 sm:hidden md:hidden">
                        <button class="px-4 py-2 whitespace-nowrap hover:text-blue-700 hover:underline">Mark as read</button>
                        <button class="px-4 py-2 whitespace-nowrap hover:text-red-700 hover:underline">Clear all</button>
                    </div> 
            </div>

            
        </div>
    </div>

 
 
 </template>
 

<script>

import { mapGetters } from 'vuex'; // ✅ Import mapGetters
import Notification from '../../components/admin/Notification/Notification.vue';
import Profile from '../../components/admin/Notification/Profile.vue';


export default {

    components: {
        Notification,
        Profile,

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