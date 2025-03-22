<template>
  <div >
    <!-- Modal Button -->
    <button
      @click="resetCount"
      :class="{
        'bg-custom-blue dark:bg-custom-hover': isClicked,
        'hover:bg-custom-blue  hover:dark:bg-custom-hover ': !isClicked,
        'text-white': isClicked,
        'hover:text-white': !isClicked,
        'active:bg-custom-blue': isClicked
      }"
      class="relative flex items-center justify-center w-12 h-12 cursor-pointer transition duration-300 focus:outline-none rounded-full"
    >
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
        <path
          fill-rule="evenodd"
          d="M5.25 9a6.75 6.75 0 0 1 13.5 0v.75c0 2.123.8 4.057 2.118 5.52a.75.75 0 0 1-.297 1.206c-1.544.57-3.16.99-4.831 1.243a3.75 3.75 0 1 1-7.48 0 24.585 24.585 0 0 1-4.831-1.244.75.75 0 0 1-.298-1.205A8.217 8.217 0 0 0 5.25 9.75V9Zm4.502 8.9a2.25 2.25 0 1 0 4.496 0 25.057 25.057 0 0 1-4.496 0Z"
          clip-rule="evenodd"
        />
      </svg>
      <!-- Show the notification count next to the bell -->
      <span v-if="unreadCount > 0" class="absolute top-[2px] right-[2px] bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
        {{ unreadCount }}
      </span>
    </button>

    <!-- Modal -->
    <div v-if="modalNotification" class="absolute inset-0 flex items-center justify-end bg-opacity-50">
      <!-- Background Overlay -->
      <div @click="closeModal" class="fixed inset-0 z-10 opacity-50"></div>

      <!-- Modal Content -->
      <div
        class="bg-white dark:bg-custom-table border-1 shadow-lg rounded-md px-4 py-20 z-20 mx-4 w-full max-w-sm h-[550px] absolute top-20 right-16 "
      >
        <!-- Notif Content -->
        <div class="relative top-[-30px] space-y-6">
          <div class="flex justify-between">
            <h1 class="font-bold text-2xl">Notification</h1>
            <p class="relative top-1 cursor-pointer hover:text-blue-500">See all</p>
          </div>

          <div
            class="overflow-y-auto max-h-[400px] p-2"
            @scroll="loadMore"
          >
            <table v-if="notif.length" class="border-gray-300">
              <tbody>
                <tr v-for="(notification, index) in paginateNotif" :key="index" class="border-b border-gray-200">
                  <td class="p-2 px-4 rounded-lg flex flex-col hover:bg-gray-100 dark:hover:bg-custom-hover">
                    {{ notification.action }}
                    <span class="text-blue-400 text-sm">
                      {{ timeAgo(notification.timestamp) }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import dayjs from 'dayjs';
import {mapGetters} from 'vuex';


export default {
  data() {
    return {
      modalNotification: false, 
      notif: [], // ✅ Store notifications here
      unreadCount: 0,
      current_page: 1, // ✅ Start at 1 for pagination
      perPage:8,
      isClicked: false, // ✅ Add this line
    };
  },

  mounted() {
  // ✅ Load stored notifications
  const storedNotif = localStorage.getItem(`notification_${this.userRole}`);
  this.notif = storedNotif ? JSON.parse(storedNotif) : [];

  // ✅ Load unread count from storage
  this.unreadCount = Number(localStorage.getItem(`unreadCount_${this.userRole}`)) || 0;

  // ✅ Filter out old notifications (> 24 hours)
  const now = new Date().getTime();
  this.notif = this.notif.filter(n => now - n.timestamp < 24 * 60 * 60 * 1000);

  // ✅ Subscribe to notifications based on user role
  if (['admin', 'warehouse_staff', 'procurement', 'manager'].includes(this.userRole)) {
    this.subscribeToNotifications(this.userRole);
  }
},

  methods: {
    subscribeToNotifications(role) {
    const channel = role === 'admin' ? 'admin-notification' : role === 'warehouse_staff' ? 'warehouse-notification' : role === 'procurement' ? 'procurement-notification' : 'manager-notification';
    const eventName = role === 'admin' ? '.notified-admin' : role === 'warehouse_staff' ? '.notified-warehouse' : role === 'procurement' ? '.notified-procurement' : '.notified-manager';

    window.Echo.private(channel)
      .listen(eventName, (event) => {
        console.log(`${role} event:`, event);

        // ✅ Add new notification at the top
        this.notif.unshift(event);
        this.notifCount = this.notif.length;
        this.unreadCount++;

        // ✅ Keep only the latest 50 notifications
        this.notif = this.notif.slice(0, 50);

        // ✅ Save notifications and unread count separately for each role in local storage
        localStorage.setItem(`notification_${role}`, JSON.stringify(this.notif));
        localStorage.setItem(`unreadCount_${role}`, this.unreadCount);
      });
  },
    timeAgo(timestamp) {
      return dayjs(timestamp).fromNow();
    },
  // ✅ Handle new notifications
      handleNotification(data) {
        // 👉 Check if the notification ID already exists in the notifCount array
        // `some()` returns true if any element in the array matches the condition
        if (!this.notifCount.some(n => n.id === data.id)) {
          // ✅ If the ID is new, add the notification to the beginning of the array
          // `unshift()` adds the new notification to the front of the list
          this.notifCount.unshift(data);

          // ✅ Increment the unread count since it's a new notification
          this.unreadCount++;

          // ✅ Save the unread count in localStorage so it persists after a refresh
          localStorage.setItem(`unreadCount_${this.userRole}`, this.unreadCount);
        }
      },
      resetCount() {
        if (['admin', 'warehouse_staff', 'procurement', 'manager'].includes(this.userRole)) {
          this.modalNotification = true;
          this.unreadCount = 0;
          this.isClicked = !this.isClicked;
          
          // ✅ Reset unread count for the specific user role
          localStorage.setItem(`unreadCount_${this.userRole}`, this.unreadCount);
        }
      },

    closeModal() {
      this.modalNotification = false;
      
    },

    loadMore(event) {
      const container = event.target;
      if (
        container.scrollTop + container.clientHeight >= container.scrollHeight - 10 &&
        this.current_page * this.perPage < this.notif.length
      ) {
        this.current_page++;
      }
    },
  },

  computed: {
    ...mapGetters('auth', ['user', 'userRole']),
    paginateNotif() {
      // ✅ Load more notifications on scroll
      return this.notif.slice(0, this.current_page * this.perPage);
    },
  },
};
</script>

