<template>
   
    <div class="p-8 ">
     <header class="flex flex-row justify-between items-center">
         <h1 class="text-2xl  whitespace-nowrap">Inventory</h1>
         <div class="flex items-end justify-end">
             <InventoryNotification />
             <InventoryProfile />
         </div>
     </header>
 
     <div class="flex flex-row justify-between flex-wrap relative top-10">
           <h1 class="text-lg  ">Matetials</h1>
             <div class="flex items-end justify-end space-x-6">
                 <InventorySearch Class="w-full sm:w-auto" />
                 <InventoryFillter Class="w-full sm:w-auto" />
                 <AdminAddUser Class="w-full sm:w-auto" />
             </div>
     </div>
         <div>
             <InventoryTable />
         </div>
 
 </div>
 
 
 
 
 
 
 </template>
<script>
import { mapGetters } from 'vuex';
import InventoryFillter from '../../components/admin/Inventory/InventoryFillter.vue';
import InventoryProfile from '../../components/admin/Inventory/InventoryProfile.vue';
import InventorySearch from '../../components/admin/Inventory/InventorySearch.vue';
import InventoryNotification from '../../components/admin/Inventory/InventoryNotification.vue';
import InventoryTable from '../../components/admin/Inventory/InventoryTable.vue';

export default {
    components :{
        InventoryTable,
        InventoryProfile,
        InventoryNotification,
        InventoryFillter,
        InventorySearch,
 
    },
    computed:{
        ...mapGetters(['isAuthenticated']), // Map the Vuex getter

    },

};
</script>
