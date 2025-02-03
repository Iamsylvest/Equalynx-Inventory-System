<template>
   
   <div class="p-8 ">
    <header class="flex flex-row justify-between items-center">
        <h1 class="text-2xl  whitespace-nowrap">User Management</h1>
        <div class="flex items-end justify-end">
            <AdminNotification />
            <AdminProfile />
        </div>
    </header>

    <div class="flex flex-row justify-between flex-wrap relative top-10">
          <h1 class="text-lg  ">All users ( <span> {{ total }}</span> )</h1>
            <div class="flex items-end justify-end space-x-6">
                <AdminSearch Class="w-full sm:w-auto" />
                <AdminFillter Class="w-full sm:w-auto" />
                <AdminAddUser Class="w-full sm:w-auto" />
            </div>
    </div>
        <div>
            <AdminUserTable />
        </div>

</div>



</template>

<script>
import { mapGetters } from 'vuex';
import AdminNotification from '../../components/admin/UserManagement/AdminNotification.vue';
import AdminProfile from '@/components/admin/UserManagement/AdminProfile.vue';
import AdminSearch from '../../components/admin/UserManagement/AdminSearch.vue';
import AdminFillter from '../../components/admin/UserManagement/AdminFillter.vue';
import AdminAddUser from '@/components/admin/UserManagement/AdminAddUser.vue';
import AdminUserTable from '@/components/admin/UserManagement/AdminUserTable.vue';
import axios from 'axios';


export default {
    data(){
    return{
        total:0 ,
    }
    },
    mounted() {
    // Fetch the users data from the API
    axios
      .get('/api/users') // Replace with your actual API endpoint
      .then((response) => {
        this.total = response.data.users.length; // Set total to the number of users
      })
      .catch((error) => {
        console.error('Error fetching users:', error);
      });
  },
    components :{
        AdminNotification,
        AdminProfile,
        AdminSearch,
        AdminFillter,
        AdminAddUser,
        AdminUserTable,
    },

    computed:{
        ...mapGetters(['isAuthenticated']), // Map the Vuex getter

    },

};
</script>

