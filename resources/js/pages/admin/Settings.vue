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


        <!-- Log Out Button -->
            <div class="bg-custom-blue w-[200px] rounded-md text-white  flex p-2 dark:bg-custom-table cursor:pointer px-5 border border-black dark:border-white ">
              <div>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                  <path fill-rule="evenodd" d="M16.5 3.75a1.5 1.5 0 0 1 1.5 1.5v13.5a1.5 1.5 0 0 1-1.5 1.5h-6a1.5 1.5 0 0 1-1.5-1.5V15a.75.75 0 0 0-1.5 0v3.75a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V5.25a3 3 0 0 0-3-3h-6a3 3 0 0 0-3 3V9A.75.75 0 1 0 9 9V5.25a1.5 1.5 0 0 1 1.5-1.5h6ZM5.78 8.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 0 0 0 1.06l3 3a.75.75 0 0 0 1.06-1.06l-1.72-1.72H15a.75.75 0 0 0 0-1.5H4.06l1.72-1.72a.75.75 0 0 0 0-1.06Z" clip-rule="evenodd" />
                </svg>
              </div>

              <div>
                <button @click="logout" class="">
                    <h1 class="font-bold">Log out</h1>
                </button>
              </div>
            </div>
     </div>
 </div>
             

 

 </template>
 
<script>
import { mapGetters} from "vuex";
import Notification from '@/components/admin/Notification/Notification.vue';
import Profile from '@/components/admin/Notification/Profile.vue';
import DarkModeToggle from '@/components/toggleDarkmode/DarkModeToggle.vue';
import changePassword from '@/auth/changePassword.vue';



export default {
    components:
    {
        Notification,
        Profile,
        DarkModeToggle,
        changePassword,
       
      
      
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
    },

    logout() {
        // Call Vuex action to clear the session
        this.$store.dispatch('auth/logout');

        // Redirect the user to the login page
        this.$router.push('/login');
      },

  }

}
    
</script>