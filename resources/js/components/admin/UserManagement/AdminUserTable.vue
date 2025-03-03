<template>
  <div class="mt-10 font-roboto">
    <!-- Add User Component -->
    <div class="md:flex md:flex-row md:justify-between md:items-center md:flex-wrap md:relative md:top-[-10px] mb-3">
      <h1 class="text-lg  mb-[10px]">All users ( <span> {{ total }}</span> )</h1> <br> 
            <div class="md:flex md:items-center md:justify-end space-x-6">
                  <AdminFillter @search="updateSearch" @filter="updateFilters" /> <br>
                  <AdminAddUser @userAdded="handleUserAdded"   />
            </div>
    </div>

    <table class="table-auto w-full border-collapse mt-1 shadow-lg">
      <thead class="h-14">
        <tr class="bg-custom-blue text-white">
          <th class="px-4 py-2 border-0 text-center font-bold">Username</th>
          <th class="px-4 py-2 border-0 text-center font-bold">Role</th>
          <th class="px-4 py-2 border-0 text-center font-bold">Date Added</th>
          <th class="px-4 py-2 border-0 text-center font-bold">Status</th>
          <th class="px-4 py-2 border-0 text-center font-bold">Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(user, index) in filteredUsers" :key="user.id" 
        :class="{
                'border-b': true,             /* Add bottom border */
                'border-b-gray-400': true,    /* Darker gray border color */
                'bg-white hover:bg-gray-200':true,    /* Alternating gray color for even rows */         /* Alternating white color for odd rows */    
              }"  
        >
          <td class="text-center px-4 py-2 border-0">
            <div class="items-center justify-center space-x-3">
              <span class="font-bold">{{ user.first_name }} {{ user.middle_name }} {{ user.last_name }}</span>
              <div class="text-sm text-gray-500">{{ user.email }}</div>
            </div>
          </td>
          <td class="px-4 py-4 text-center">
            <span
              class="p-2 rounded-xl text-white font-semibold"
              :class="{
                'bg-green-300 text-green-600': user.role === 'admin',
                'bg-orange-300 text-orange-600': user.role === 'manager',
                'bg-red-300 text-red-600': user.role === 'procurement',
                'bg-yellow-300 text-yellow-600': user.role === 'warehouse_staff'
              }"
            >
              {{ user.role }}
            </span>
          </td>
          <td class="text-center px-4 py-2 border-0">{{ formatDate(user.created_at) }}</td>
          <td class="text-center px-4 py-2 border-0"
          :class="user.is_active ? 'text-green-600' : 'text-red-600'"
          >{{ user.is_active ? 'Active' : 'Inactive' }}</td>
          <td class="text-center px-4 py-2 border-0 md:space-x-3">
            <button class="text-gray-500 hover:underline w-full md:w-auto" @click="showEditModal(user)">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487 18.5 2.75a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
              </svg>
            </button>
              <button @click="deleteUser(user.id)" class="text-gray-500 hover:underline w-full lg:w-auto ">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
              </button>
          </td>
        </tr>
      </tbody>
      
    </table>

      <div class="flex items-center justify-center py-2 px-4 bg-white shadow-md z-50">
              <!-- Previous Button -->
              <button 
                @click="fetchUsers(currentPage - 1)" 
                :disabled="currentPage === 1" 
                class="text-lg px-4 py-2 rounded-lg disabled:opacity-2 hover:bg-gray-100 cursor-pointer"
              >
                ←
              </button>

              <!-- Pagination Numbers -->
              <span 
                v-for="page in lastPage" 
                :key="page"
                @click="fetchUsers(page)"
                class="mx-2 px-3 py-2 cursor-pointer rounded-lg"
                :class="{'bg-blue-500 text-white': currentPage === page, 'hover:bg-gray-200': currentPage !== page}"
              >
                {{ page }}
              </span>

              <!-- Next Button -->
              <button 
                @click="fetchUsers(currentPage + 1)" 
                :disabled="currentPage === lastPage" 
                class="text-lg px-4 py-2 rounded-lg disabled:opacity-2 hover:bg-gray-100 cursor-pointer"
              >
                →
              </button>
        </div>

      <!-- Edit User Modal -->
      <AdminEditUser 
      :isVisible="isEditModalVisible"
      :user="editingUser"
      @close="closeEditModal" 
      @update="updateUser"  
    /> 
     </div>
    
          
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import AdminEditUser from '@/components/admin/UserManagement/AdminEditUser.vue';
import AdminAddUser from '@/components/admin/UserManagement/AdminAddUser.vue';
import AdminFillter from '@/components/admin/UserManagement/AdminFillter.vue';

export default {
  components: {
    AdminAddUser,
    AdminEditUser,
    AdminFillter,

  },
  data() {
    return {
      users: [],
      userToDeleteId: null,
      isEditModalVisible: false,
      editingUser: null,
      currentPage: 1,
      lastPage: 1, // ✅ Add this to prevent the Vue warning
      total: 0,
      searchQuery: "",
      selectedRole: "",
      selectedStatus: "",
    };
  },
  computed: {
    filteredUsers() {
      return this.users
        .filter(user => {
          const fullName = `${user.first_name} ${user.last_name}`.toLowerCase();
          const email = user.email.toLowerCase();
          return this.searchQuery ? fullName.includes(this.searchQuery.toLowerCase()) || email.includes(this.searchQuery.toLowerCase()) : true;
        })
        .filter(user => (this.selectedRole ? user.role === this.selectedRole : true))
        .filter(user => (this.selectedStatus !== "" ? user.is_active == this.selectedStatus : true));
    }
  },

  mounted() {
    this.fetchUsers();
  },
  methods: {
      fetchUsers(page = 1) {
      // Check if the selectedStatus or selectedRole are not empty before appending them to the URL
      let url = `/api/users?page=${page}`;
      if (this.selectedStatus !== "") {
        url += `&status=${this.selectedStatus}`;
      }
      if (this.selectedRole !== "") {
        url += `&role=${this.selectedRole}`;
      }

      axios.get(url)
        .then(response => {
          this.users = response.data.data;
          this.total = response.data.total;
          this.currentPage = response.data.current_page;
          this.lastPage = response.data.last_page;
        })
        .catch(error => {
          console.error('Error fetching users:', error.response ? error.response.data : error);
        });
    },

    updateSearch(query) {
      this.searchQuery = query;
      this.fetchUsers(1); // ✅ Reset to first page when searching
    },
    updateFilters(filters) {
      this.selectedRole = filters.role;
      this.selectedStatus = filters.status;
      this.fetchUsers(1); // ✅ Reset to first page when filtering
    },


    showEditModal(user) {
      console.log("Opening Edit User Modal for", user);
      this.editingUser = user;
      this.isEditModalVisible = true;
    },

    closeEditModal() {
      console.log("Closing Edit User Modal");
      this.isEditModalVisible = false;
      this.editingUser = null;
    },

    deleteUser(userId) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!"
      }).then((result) => {
        if (result.isConfirmed) {
          axios.delete(`/api/users/${userId}`)
            .then(() => {
              this.users = this.users.filter(user => user.id !== userId);
              this.total--; // Update total count dynamically
              
              Swal.fire("Deleted!", "The user has been deleted.", "success");
            })
            .catch(error => {
              console.error("Error deleting user:", error);
              Swal.fire("Error!", "Something went wrong.", "error");
            });
        }
      });
    },

    updateUser(updatedUser) {
      axios
        .patch(`/api/users/${updatedUser.id}`, updatedUser) // Send update request
        .then((response) => {
          const updatedData = response.data.user; // ✅ Corrected this line

          // ✅ Find and update the user in the paginated list
          const index = this.users.findIndex(user => user.id === updatedData.id);
          if (index !== -1) {
            this.users[index] = updatedData;
          }

          this.closeEditModal();

          // ✅ Fetch updated users from API to ensure accuracy
          this.fetchUsers(this.currentPage);

          // ✅ Show success alert
          Swal.fire({
            title: "Success!",
            text: "User details updated successfully.",
            icon: "success",
            confirmButtonColor: "#3085d6",
          });
        })
        .catch(error => {
          console.error("Error updating user:", error);
          Swal.fire("Error!", "Failed to update user.", "error");
        });
    },
    formatDate(date) {
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    },

    handleUserAdded(newUser) {
        if (this.currentPage === this.lastPage) {
          // If on the last page, the new user will be added on the same page
          this.users.push(newUser);
        } else {
          // Otherwise, fetch the current page again to prevent showing on the first page
          this.fetchUsers(this.currentPage);
        }
        this.total++; // Update total count dynamically
        
      Swal.fire({
        title: "Success!",
        text: "New user added successfully.",
        icon: "success",
        confirmButtonColor: "#3085d6",
      });
    }
  }
};
</script>