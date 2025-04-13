<template>
    <div class="">
<div >
               <!-- Form Header -->
    <div class="bg-custom-blue p-4 rounded-md shadow-sm dark:bg-custom-hover">
        <h2 class="text-lg font-bold text-white">Personal Information</h2>
    </div>

    <!-- Section 1: Personal Information -->
    <div  class="bg-white drop-shadow-md p-4 rounded-md shadow-sm mt-5 dark:bg-custom-table ">
        <div class="grid grid-cols-1 md:grid-cols-2  gap-4">
        <div>
            <label for="Name" class="block text-sm font-semibold text-gray-700 dark:text-custom-white">Name:</label>
            <input 
            v-model="fullname"
            type="text" 
            id="Name" 
            name="Name" 
            placeholder="Enter your name"
            class="mt-1 w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400  dark:bg-custom-table dark:focus:ring-white "
            readonly/>
        </div>

        <div>
            <label for="Email" class="block text-sm font-semibold text-gray-700   dark:text-custom-white ">Email:</label>
            <input 
            v-model="userEmail"
            type="email" 
            id="Email" 
            name="Email" 
            placeholder="Enter your email"
            class="mt-1 w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400  dark:bg-custom-table dark:focus:ring-white "
            readonly/>
        </div>
        </div>
    </div>

    <!-- Section 2: Employee Information -->
    <div class="bg-white drop-shadow-md mt-5 p-4 rounded-md shadow-sm  dark:bg-custom-table">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="Department" class="block text-sm font-semibold text-gray-700 dark:text-custom-white">Department/Position:</label>
            <input 
            v-model="role"
            type="text" 
            id="Department" 
            name="Department" 
            placeholder="Enter department/position"
            class="mt-1 w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400  dark:bg-custom-table dark:focus:ring-white "
            readonly
            />
        </div>
        </div>
    </div>


              <!-- Change Password Form -->
      <form @submit.prevent="submitPasswordChange" class="mt-5 p-4 rounded-md shadow-sm  dark:bg-custom-table w-full bg-white drop-shadow-md">
                <label for="current_password" class="block text-sm font-semibold text-gray-700 dark:text-custom-white" >Current Password :</label>
                <input 
                v-if="showPassword" 
                type="password" 
                v-model="current_password" 
                id="current_password" required  
                class="mt-1 w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400  dark:bg-custom-table dark:focus:ring-white "/>
                <input v-if="!showPassword" 
                type="text" 
                v-model="current_password" 
                id="current_password" 
                class="mt-1 w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400  dark:bg-custom-table dark:focus:ring-white "/>

                <button  
                @click="togglePasswordVisibility" 
                class="mt-1 w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
                 {{  showPassword ? 'hide' : 'show' }}
                </button>
                    <label for="new_password">New Password :</label>
                    <input type="password"
                     v-model="new_password" 
                     id="new_password" required 
                     class="mt-1 w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400  dark:bg-custom-table dark:focus:ring-white " />

            <div >
                    <label for="confirm_password ">Confirm New Password :</label>
                    <input 
                    type="password" 
                    v-model="confirm_password" 
                    id="confirm_password" 
                    required 
                    class="mt-1 w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400  dark:bg-custom-table dark:focus:ring-white " /> <br><br>

                    <div>
                        <button type="submit"    class="mt-1 w-full p-2 border rounded-md focus:outline-none focus:ring-2 bg-custom-blue text-custom-white focus:ring-blue-400  dark:bg-custom-hover dark:focus:ring-white ">Change Password</button>
                    </div>
            </div>
        </form>
        
    </div>
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
        userDetails: {},
        showPassword: false, // Initially set to false to hide password
      };
    },
    methods: {
        togglePasswordVisibility(){
            this.showPassword = !this.showPassword;
        },
        async submitPasswordChange() {
    try {
        const user = this.$store.state.auth.user?.data;

        if (!user || !user.id) {
            console.error("❌ Error: User ID is missing!", user);
            return;
        }

        // Validate inputs
        if (!this.current_password) {
            Swal.fire({ title: 'Error', text: 'Current password is required!', icon: 'error' });
            return;
        }
        if (!this.new_password || !this.confirm_password) {
            Swal.fire({ title: 'Error', text: 'New password and confirmation are required!', icon: 'error' });
            return;
        }
        if (this.new_password !== this.confirm_password) {
            Swal.fire({ title: 'Error', text: 'New password and confirm password do not match!', icon: 'error' });
            return;
        }

        // Debug Payload
        console.log("🔍 Payload before sending:", {
            current_password: this.current_password,
            new_password: this.new_password,
            new_password_confirmation: this.confirm_password
        });

        // Send request
        const payload = {
            current_password: this.current_password,
            new_password: this.new_password,
            new_password_confirmation: this.confirm_password,
        };

        const response = await axios.patch(`/api/password-change/${user.id}`, payload, {
            headers: { "Content-Type": "application/json" }
        });

        console.log("✅ Password changed successfully:", response.data);

        Swal.fire({ title: 'Success', text: 'Password changed successfully!', icon: 'success' });

        // Clear input fields
        this.current_password = "";
        this.new_password = "";
        this.confirm_password = "";

    } catch (error) {
        console.error("❌ Error changing password:", error.response?.data || error.message);

        if (error.response?.status === 422) {
            Swal.fire({ title: 'Error', text: 'Invalid password. Please try again!', icon: 'error' });
        } else {
            Swal.fire({ title: 'Error', text: error.response?.data?.message || 'Something went wrong!', icon: 'error' });
        }
    }
},

    async fetchUserDetails(){
          try{
            const response = await axios.get('/api/get_user_details');
                console.log("User details fetched successfully", response.data);
                 // Correctly assign response data to userDetails
                 this.userDetails = response.data;   
          }catch(error){
            console.error("User details failed to fetcj", error)
          }
    }
},
computed: {
    fullname() {
        if (this.userDetails) {
            return `${this.userDetails.first_name} ${this.userDetails.middle_name || ''} ${this.userDetails.last_name}`.trim();
        }
        return ''; // Default empty if userDetails is not loaded
    },
    userEmail(){
        if(this.userDetails){
            return `${this.userDetails.email}`.trim();
        }
        return '';
    },
    role(){
        if(this.userDetails){
            return `${this.userDetails.role}`.trim();
        }
        return '';
    }
},
mounted(){
    this.fetchUserDetails();
}
  };

  </script>