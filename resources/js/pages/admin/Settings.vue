<template>
   
    <div class="p-8 dark:text-custom-white ">
     <header class="flex flex-row justify-between items-center">
         <h1 class="text-2xl  whitespace-nowrap">Settings</h1>
         <div class="hidden sm:flex md:flex items-end justify-end ">
            <Notification/>
            <Profile/>
         </div>
     </header>
     <div class=" flex flex-col space-y-5 font-roboto p-8 mt-16  border  shadow-lg  dark:border-none rounded-lg dark:bg-custom-table">
        <p>Mange your account setting and preferences</p> 
        <DarkModeToggle />
        <changePassword />

        <thresholdUpdate v-if="userRole === 'admin'" />
     </div>

              
 
 </div>
 
 

 </template>
 
<script>
import { mapGetters} from "vuex";
import Notification from '@/components/admin/Notification/Notification.vue';
import Profile from '@/components/admin/Notification/Profile.vue';
import DarkModeToggle from '@/components/toggleDarkmode/DarkModeToggle.vue';
import changePassword from '@/auth/changePassword.vue';
import thresholdUpdate from '@/components/Settings/thresholdUpdate.vue';

export default {
    components:
    {
        Notification,
        Profile,
        DarkModeToggle,
        changePassword,
        thresholdUpdate,
      
    },
    computed:{
        ...mapGetters( 'auth',['user', 'userRole']),
    },
    data() {
      return {
        current_password: "",
        new_password: "",
        confirm_password: "",
        showPassword: false, // Initially set to false to hide password
      };
    },
    methods: {
        togglePasswordVisibility(){
            this.showPassword = !this.showPassword;
        },
        async submitPasswordChange() {
    try {
        const user = this.$store.state.auth.user?.data; // Get user from Vuex
        console.log("User data from Vuex:", user); // Debugging step

        if (!user || !user.id) {
            console.error("❌ Error: User ID is missing!", user);
            return;
        }

        // Check if the new password and confirm password match
        if (this.newPassword !== this.confirmPassword) {
                console.error("❌ Error: Passwords do not match!");
                Swal.fire({
                    title: 'Error',
                    text: 'New password and confirm password do not match!',
                    icon: 'error'
                });
                return;
            }

            const payload = {
                current_password: this.currentPassword,
                new_password: this.newPassword,
                new_password_confirmation: this.confirmPassword,
            };

            const response = await axios.patch(`/api/password-change/${user.id}`, payload);

            console.log("✅ Password changed successfully:", response.data);

            Swal.fire({
                title: 'Success',
                text: 'Password changed successfully!',
                icon: 'success'
            });

            // Clear input fields after success
            this.currentPassword = "";
            this.newPassword = "";
            this.confirmPassword = "";
            
        } catch (error) {
            console.error("❌ Error changing password:", error.response?.data || error.message);
            
            // Handle specific error cases
            if (error.response?.status === 400) {
                // Laravel validation error (e.g., incorrect current password)
                if (error.response.data.errors?.current_password) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Current password is incorrect!',
                        icon: 'error'
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: error.response.data.message || 'Validation failed!',
                        icon: 'error'
                    });
                }
            } else {
                // General error
                Swal.fire({
                    title: 'Error',
                    text: error.response?.data?.message || 'Something went wrong!',
                    icon: 'error'
                });
            }
        }
    }

  }

}
    
</script>