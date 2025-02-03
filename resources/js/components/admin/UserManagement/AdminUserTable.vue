<template>
  <div class="mt-16">
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
        <tr v-for="(user, index) in users" :key="user.id" :class="index % 2 === 0 ? 'bg-gray-100' : 'bg-white'">
          <td class="text-center px-4 py-2 border-0">
            <div class="items-center justify-center space-x-3">
              <!-- Full Name -->
              <span class="font-bold">{{ user.first_name }} {{ user.middle_name }} {{ user.last_name }}</span>
              <!-- Email -->
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
          <td class="text-center px-4 py-2 border-0">{{ user.is_active ? 'Active' : 'Inactive' }}</td>
          <td class="text-center px-4 py-2 border-0 space-x-3">
            <!-- Edit Button -->
            <button class="text-gray-500 hover:underline w-full sm:w-auto" @click="showEditModal(user)">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487 18.5 2.75a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
              </svg>
            </button>
            <button class="text-gray-500 hover:underline w-full sm:w-auto" @click="showModal(user.id)">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
              </svg>
            </button>
          </td>
        </tr>
      </tbody>
    </table>
    
    <!-- Modal -->
    <div v-if="isModalVisible" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center z-50">
      <div class="bg-white p-8 rounded-md w-96">
        <h2 class="text-xl font-semibold mb-4">Are you sure you want to delete this user?</h2>
        <div class="flex justify-end space-x-4">
          <button @click="deleteUser" class="bg-red-500 text-white px-4 py-2 rounded-md">Yes, Delete</button>
          <button @click="closeModal" class="bg-gray-300 text-black px-4 py-2 rounded-md">Cancel</button>
        </div>
      </div>
    </div>

    <!-- Edit User Modal -->
    <AdminEditUser 
      :isVisible="isEditModalVisible"
      :user="editingUser"
      @close="closeEditModal" 
      @update="updateUser"  
    /> 

    <div v-if="isLoading" class="text-center">
      <span>Loading...</span> 
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import AdminEditUser from '@/components/admin/UserManagement/AdminEditUser.vue';
import AdminAddUser from '@/components/admin/UserManagement/AdminAddUser.vue';

export default {
  components: {
    AdminAddUser,
    AdminEditUser,
  },
  data() {
    return {
      users: [],
      isModalVisible: false,
      userToDeleteId: null,
      isEditModalVisible: false,
      editingUser: null,
      isLoading: false,
    };
  },
  mounted() {
    this.fetchUsers();
  },
  methods: {
    fetchUsers() {
      axios.get('/api/users')
        .then(response => {
          this.users = response.data.users;
        })
        .catch(error => {
          console.error('Error fetching users:', error);
        });
    },
    formatDate(date) {
      const formattedDate = new Date(date);
      return formattedDate.toLocaleDateString();
    },
    showModal(userId) {
      this.isModalVisible = true;
      this.userToDeleteId = userId;
    },
    closeModal() {
      this.isModalVisible = false;
      this.userToDeleteId = null;
    },
    deleteUser() {
      axios.delete(`/api/users/${this.userToDeleteId}`)
        .then(response => {
          this.users = this.users.filter(user => user.id !== this.userToDeleteId);
          this.closeModal();
        })
        .catch(error => {
          console.error('Error deleting user:', error);
        });
    },
    showEditModal(user) {
      this.editingUser = user;
      this.isEditModalVisible = true;
    },
    closeEditModal() {
      this.isEditModalVisible = false;
      this.editingUser = null;
    },
    updateUser(updatedUser) {
      this.isLoading = true;  // Set loading state to true
      axios.put(`/api/users/${updatedUser.id}`, updatedUser)
        .then(response => {
          const index = this.users.findIndex(user => user.id === updatedUser.id);
          if (index !== -1) {
            this.users[index] = updatedUser;  // Update the user data in the array
          }
          this.isLoading = false;  // Reset loading state
          this.closeEditModal();
        })
        .catch(error => {
          console.error('Error updating user:', error);
          this.isLoading = false;  // Reset loading state in case of error
        });
    },
    handleUserAdded(newUser) {
      this.users.push(newUser);  // Add the new user to the list
    }
  }
};
</script>