<template>
    <div class="p-7">
      <!-- Change Password Form -->
      <form @submit.prevent="submitPasswordChange" class="space-y-5">
        <div>
            <h2>Change Your Password</h2>
        </div>
        <div class="space-y-5"  >
            <div class="space-x-24 flex ">
                <label for="current_password">Current Password :</label>
                <input v-if="showPassword" type="password" v-model="current_password" id="current_password" required  class=" dark:bg-custom-table border rounded-lg p-1"/>
                <input v-if="!showPassword" type="text" v-model="current_password" id="current_password" required  class=" dark:bg-custom-table border rounded-lg"/>
                <button  @click="togglePasswordVisibility" class="relative right-36 text-xs" >
                        {{  showPassword ? 'hide' : 'show' }}
                </button>
            </div>
        
            <div  class="space-x-28 ">
                    <label for="new_password">New Password :</label>
                    <input type="password" v-model="new_password" id="new_password" required class=" dark:bg-custom-table border rounded-lg p-1" />
            </div>
       
            <div  class="space-x-12 ">
                    <label for="confirm_password ">Confirm New Password :</label>
                    <input type="password" v-model="confirm_password" id="confirm_password" required class=" dark:bg-custom-table border rounded-lg p-1" /> <br><br>
                    <div>
                        <button type="submit" class=" bg-custom-blue text-custom-white dark:bg-custom-table border rounded-lg text-sm p-2 px-9 relative left-44">Change Password</button>
                    </div>
                   
            </div>
            <div >
               
            </div>
      
        </div>
  
     
      </form>
    </div>
  </template>
  <script>

  import axios from 'axios';
  import { mapGetters } from 'vuex';
  import Swal from 'sweetalert2';
  export default {
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
  };

  </script>