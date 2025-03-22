<template>
   
  <div class="p-8 dark:text-custom-white ">
   <header class="flex flex-row justify-between items-center">
       <h1 class="text-2xl  whitespace-nowrap">Transaction</h1>
       <div class="hidden sm:flex md:flex items-end justify-end">
           <Notification />
           <Profile />
       </div>
   </header>

       <div>
           <TransactionTable />
       </div>

</div>



</template>

  
  <script>
  import { mapGetters } from 'vuex'; // Ensure this is at the top
  import Notification from '@/components/admin/Notification/Notification.vue';
  import Profile from '@/components/admin/Notification/Profile.vue';
  import TransactionTable from '@/components/admin/Transaction/TransactionTable.vue';

  
  export default {
    components: {
      Notification,
      Profile,
      TransactionTable,
  
    },
    data() {
      return {
        showTable: 'delivery' // Default value set to 'delivery'
      };
    },
    computed: {
      ...mapGetters(['isAuthenticated']) // Map the Vuex getter
    },

};
</script>
