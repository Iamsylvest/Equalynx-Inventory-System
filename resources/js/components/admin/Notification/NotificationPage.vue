<template>
          <!-- Notif Content -->
          <div class="relative top-[-30px] space-y-6">
            <div class="flex justify-between">
            </div>
            <div
              class="overflow-y-auto max-h-[400px] p-2"
              @scroll="loadMore"
            >
              <table v-if="notif.length" class="border-gray-300 w-full">
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