<template>
   
   <div class="p-8">
    <header class="flex flex-row justify-between items-center">
        <h1 class="text-2xl whitespace-nowrap">User Management</h1>
        <div class="flex items-end justify-end">
            <AdminNotification />
            <AdminProfile />
        </div>
    </header>

    <div class="flex flex-row justify-between items-center w-full sm:w-auto space-x-6 sm:mt-0 relative top-12">
            <h2 class="text-l whitespace-nowrap ">All Users (#)</h2>
            <div class="flex items-end justify-end space-x-6">
                <AdminSearch Class="w-full sm:w-auto" />
                <AdminFillter Class="w-full sm:w-auto" />
                <AdminAddUser Class="w-full sm:w-auto" />
            </div>
    </div>





</div>






</template>

<script>
import { mapGetters } from 'vuex';
import AdminNotification from '../../components/admin/UserManagement/AdminNotification.vue';
import AdminProfile from '../../components/admin/UserManagement/AdminProfile.vue';
import AdminSearch from '../../components/admin/UserManagement/AdminSearch.vue';
import AdminFillter from '../../components/admin/UserManagement/AdminFillter.vue';
import AdminAddUser from '../../components/admin/UserManagement/AdminAddUser.vue';


export default {
    components :{
        AdminNotification,
        AdminProfile,
        AdminSearch,
        AdminFillter,
        AdminAddUser,
 
    },
    computed:{
        ...mapGetters(['isAuthenticated']), // Map the Vuex getter

    },

};
</script>

