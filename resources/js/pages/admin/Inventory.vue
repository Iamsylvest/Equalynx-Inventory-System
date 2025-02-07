<template>
   
    <div class="p-8 ">
     <header class="flex flex-row justify-between items-center">
         <h1 class="text-2xl  whitespace-nowrap">Inventory</h1>
         <div class="flex items-end justify-end">
             <InventoryNotification />
             <InventoryProfile />
         </div>
     </header>
 
         <div>
             <InventoryTable />
         </div>
 
 </div>
 
 
 </template>

<script>
import { mapGetters } from 'vuex';
import InventoryProfile from '../../components/admin/Inventory/InventoryProfile.vue';
import InventoryNotification from '../../components/admin/Inventory/InventoryNotification.vue';
import InventoryTable from '../../components/admin/Inventory/InventoryTable.vue';

export default {
    components :{
        InventoryTable,
        InventoryProfile,
        InventoryNotification,

 
    },
    computed:{
        ...mapGetters(['isAuthenticated']), // Map the Vuex getter

    },

};
</script>
