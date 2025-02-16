<template>
   
  <div class="p-8 ">
   <header class="flex flex-row justify-between items-center">
       <h1 class="text-2xl  whitespace-nowrap">Transaction</h1>
       <div class="flex items-end justify-end">
           <TransactionNotification />
           <TransactionProfile />
       </div>
   </header>

       <div>
           <TransactionTable />
       </div>

</div>



</template>

  
  <script>
  import { mapGetters } from 'vuex'; // Ensure this is at the top
  import TransactionNotification from '../../components/admin/Transaction/TransactionNotification.vue';
  import TransactionProfile from '../../components/admin/Transaction/TransactionProfile.vue';
  import TransactionTable from '../../components/admin/Transaction/TransactionTable.vue';

  
  export default {
    components: {
      TransactionNotification,
      TransactionProfile,
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
