<template>
   
   <div class="p-8 ">
    <header class="flex flex-row justify-between items-center">
        <h1 class="text-2xl  whitespace-nowrap">User Management</h1>
        <div class="flex items-end justify-end">
            <AdminNotification />
            <AdminProfile />
        </div>
    </header>

        <div>
            <AdminUserTable />
        </div>

</div>



</template>

<script>
import { mapGetters } from 'vuex';
import AdminNotification from '../../components/admin/UserManagement/AdminNotification.vue';
import AdminProfile from '@/components/admin/UserManagement/AdminProfile.vue';
import AdminUserTable from '@/components/admin/UserManagement/AdminUserTable.vue';



export default {
    components :{
        AdminNotification,
        AdminProfile,

        AdminUserTable,
    },

    computed:{
        ...mapGetters(['isAuthenticated']), // Map the Vuex getter

    },

};
</script>

