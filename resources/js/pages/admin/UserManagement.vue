<template>
   
   <div class="p-8 dark:text-custom-white ">
    <header class="flex flex-row justify-between items-center">
        <h1 class="text-2xl whitespace-nowrap">User Management</h1>
        <div class="hidden sm:flex md:flex lg:flex items-end justify-end">
            <Notification />
            <Profile />
        </div>
    </header>

        <div>
            <AdminUserTable />
        </div>

</div>



</template>

<script>
import { mapGetters } from 'vuex';
import Notification from '@/components/admin/Notification/Notification.vue';
import Profile from '@/components/admin/Notification/Profile.vue';
import AdminUserTable from '@/components/admin/UserManagement/AdminUserTable.vue';



export default {
    components :{
       Notification,
       Profile,
        AdminUserTable,
    },

    computed:{
        ...mapGetters(['isAuthenticated']), // Map the Vuex getter

    },

};
</script>

