<template>
  <div class="md:flex md:items-center gap-3">
   <!-- Search Bar with Button 
   <div class="mt-5 sm:mt-0 md:mt-0 lg:mt-0 flex items-center gap-2">
     <input
       v-model="tempSearchQuery"
       type="text"
       placeholder="Search by name..."
       class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-40"
     />
     <button
       @click="applySearch"
       class="px-3 py-1 bg-blue-500 text-white text-sm font-semibold rounded-md hover:bg-blue-400 transition"
     >
       Search
     </button>
   </div>

   -- Threshold Filter 
   <select
     id="threshold"
     v-model="selectedStocks"
     @change="applyFilters"
     class="mt-5 sm:mt-0 md:mt-0 lg:mt-0 border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer w-full sm:w-32 md:w-32"
   >
     <option value="">All Stock Level</option>
     <option value="Low">Low</option>
     <option value="Normal">Normal</option>
     <option value="High">High</option>
   </select>


   -- Date Added Filter 
   <div class="flex items-center gap-2 mt-5 sm:mt-0 md:mt-0 lg:mt-0">
     <label for="date_added" class="text-sm text-gray-700 w-full">Date Added</label>
     <input
       id="date_added"
       type="date"
       v-model="selectedDateAdded"
       @change="applyFilters"
       class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer w-full sm:w-36 md:w-36"
     />
   </div>

   -- Last Update Filter 
   <div class="flex items-center gap-2 mt-5 sm:mt-0 md:mt-0 lg:mt-0">
     <label for="last_update" class="text-sm text-gray-700 w-full">Last Update</label>
     <input
       id="last_update"
       type="date"
       v-model="selectedLastUpdate"
       @change="applyFilters"
       class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer w-full sm:w-36 md:w-36"
     />
   </div> -->

   <div class="grid grid-cols-2 grid-rows-3 sm:flex md:flex h-[250px] sm:h-full md:h-full sm:pt-0 md:pt- gap-6 ">

     <div class="col-span-2 mt-5 sm:mt-0 md:mt-0 lg:mt-0 flex items-center gap-2">
      <input
        v-model="tempSearchQuery"
        type="text"
        placeholder="Search by name..."
        class="dark:bg-custom-table border md:w-[148px] w-full dark:focus:ring-white border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
      />
     </div>

     <div class="col-span-2 mt-5 sm:mt-0 md:mt-0 lg:mt-0 flex items-center gap-2">
      <select
      v-model="selectedRole"
      @change="applyFilters"
      class="md:w-[148px] w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-custom-table dark:focus:ring-white"
    >
      <option value="">All Roles</option>
      <option value="admin">Admin</option>
      <option value="manager">Manager</option>
      <option value="procurement">Procurement</option>
      <option value="warehouse_staff">Warehouse Staff</option>
    </select>
     </div>

     <div class="col-span-2 h-[35px]">
  <div class="flex items-center gap-2 sm:mt-0 md:mt-0 lg:mt-0">
    <label for="date_added" class="text-sm text-gray-700 dark:text-white whitespace-nowrap">Date Added</label>
    <input
      id="date_added"
      type="date"
      v-model="selectedDate"
      @change="applyFilters"
      class="mr-5 w-[240px] md:w-[152px]  border border-gray-300 rounded-lg px-4 h-[41px]  focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-custom-table dark:focus:ring-white custom-date-input"
    />
  </div>
</div>
   </div>
 </div> 
</template>

<script>



export default {
 data () {
   return{
     tempSearchQuery: "",
     selectedRole: "",
     selectedDate: "",
   }
 },
 watch:{
     tempSearchQuery(newQuery){
       this.$emit("search", newQuery)
     }
 },
 methods: {
   
   applyFilters () {
     this.$emit( "filter",{

       role: this.selectedRole,
       created_at: this.selectedDate,
      })
    
   }
},

}

</script>