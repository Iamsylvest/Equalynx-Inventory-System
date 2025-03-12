<template>
  <div>
    <!-- Modal Button -->
    <button
      @click="resetCount"
      :class="{
        'bg-custom-blue': isClicked,
        'hover:bg-custom-blue ': !isClicked,
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
      <span v-if="notifCount > 0" class="absolute top-[2px] right-[2px] bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
        {{ notifCount }}
      </span>
    </button>

    <!-- Modal -->
    <div v-if="modalNotification" class="absolute inset-0 flex items-center justify-end bg-opacity-50">
      <!-- Background Overlay -->
      <div @click="closeModal" class="fixed inset-0 z-10 opacity-50"></div>

      <!-- Modal Content -->
      <div
        @click.stop
        class="bg-white border-1 shadow-lg rounded-md px-4 py-14 z-20 mx-4 w-full max-w-sm h-[550px] relative left-[-20px] min-w-[200px] max-h-[700px] min-h-[300px]"
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
                  <td class="p-2 px-4 rounded-lg flex flex-col hover:bg-gray-100">
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
import relativeTime from 'dayjs/plugin/relativeTime';
import {mapGetters} from 'vuex';
dayjs.extend(relativeTime);

export default {
  data() {
    return {
      modalNotification: false,
      notif: [],
      notifCount: 0,
      unreadCount: 0,
      current_page: 1, // ✅ Start at 1 for pagination
      perPage:5,
    };
  },

  mounted() {8
  // ✅ Load stored notifications
  const storedNotif = localStorage.getItem('notification');
  this.notif = storedNotif ? JSON.parse(storedNotif) : [];

  // ✅ Filter out old notifications (> 24 hours)
  const now = new Date().getTime();
  this.notif = this.notif.filter(n => now - n.timestamp < 24 * 60 * 60 * 1000);

  // ✅ Load unread count from storage
  this.unreadCount = Number(localStorage.getItem('unreadCount')) || 0;

  // ✅ Only subscribe if the user is an admin or warehouse staff
  if (this.userRole === 'warehouse_staff' || this.userRole === 'admin') {
    window.Echo.private('warehouse-notification')
      .listen('.notified-warehouse', (event) => {
        console.log('Warehouse event:', event);

        // ✅ Add new notification at the top
        this.notif.unshift(event);
        this.notifCount = this.notif.length;
        this.unreadCount++;

        // ✅ Keep only the latest 50 notifications
        this.notif = this.notif.slice(0, 50);

        // ✅ Save to local storage
        localStorage.setItem('notification', JSON.stringify(this.notif));
        localStorage.setItem('unreadCount', this.unreadCount);
      });
  }
},

  methods: {
    timeAgo(timestamp) {
      return dayjs(timestamp).fromNow();
    },

    resetCount() {
      this.modalNotification = true;
      this.unreadCount = 0; // ✅ Reset unread count to 0
      this.notifCount = 0;    
      localStorage.setItem('unreadCount', this.unreadCount);
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

