<template>
    <div class="p-8">
      <header class="flex flex-row justify-between items-center">
        <h1 class="text-2xl whitespace-nowrap">Transaction</h1>
        <div class="flex items-end justify-end">
          <TransactionNotification />
          <TransactionProfile />
        </div>
      </header>
  
      <!-- Main container -->
      <div class="flex flex-wrap items-center justify-between relative top-10 w-full">
        <!-- Left Section: Buttons -->
        <div class="flex space-x-4">
          <button
            class="p-3 px-12 text-md font-bold border-b-2 shadow-md rounded-md focus:outline-none w-full sm:w-auto"
            @click="showTable = 'delivery'"
            :class="{
              'bg-custom-blue text-white': showTable === 'delivery',
              'hover:bg-custom-blue hover:text-white': showTable !== 'delivery'
            }"
          >
            Delivery Receipt
          </button>
  
          <button
            class="p-3 px-12 text-md font-bold border-b-2 shadow-md rounded-md focus:outline-none w-full sm:w-auto"
            @click="showTable = 'return'"
            :class="{
              'bg-custom-blue text-white': showTable === 'return',
              'hover:bg-custom-blue hover:text-white': showTable !== 'return'
            }"
          >
            Return Receipt
          </button>
        </div>
  
        <!-- Right Section: Search and Filter (Aligned to the end) -->
        <div class="flex items-center space-x-4 ml-auto">
          <TransactionSearch class="w-full sm:w-auto" />
          <TransactionFillter class="w-full sm:w-auto" />
        </div>
      </div>
  
      <!-- Tables -->
      <div class="mt-14">
        <DeliveryReceipt v-if="showTable === 'delivery'" class="w-full sm:w-auto" />
        <ReturnReceipt v-if="showTable === 'return'" class="w-full sm:w-auto" />
      </div>
    </div>
  </template>
  
  <script>
  import { mapGetters } from 'vuex';
  import TransactionNotification from '../../components/admin/Transaction/TransactionNotification.vue';
  import TransactionProfile from '../../components/admin/Transaction/TransactionProfile.vue';
  import TransactionSearch from '../../components/admin/Transaction/TransactionSearch.vue';
  import TransactionFillter from '../../components/admin/Transaction/TransactionFillter.vue';
  import DeliveryReceipt from '../../components/admin/Transaction/DeliveryReceipt.vue';
  import ReturnReceipt from '../../components/admin/Transaction/ReturnReceipt.vue';
  
  export default {
    components: {
      TransactionNotification,
      TransactionProfile,
      TransactionSearch,
      TransactionFillter,
      ReturnReceipt,
      DeliveryReceipt
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
