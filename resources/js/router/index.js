import { createRouter, createWebHistory } from 'vue-router';
import Login from '@/auth/Login.vue';
import ResetPassword from '@/auth/ResetPassword.vue';
import UserManagement from '@/pages/admin/UserManagement.vue'; 
import Inventory from '@/pages/admin/Inventory.vue';
import Transaction from '@/pages/admin/Transaction.vue';
import ActivityLogs from '@/pages/admin/ActivityLogs.vue';
import Notification from '@/pages/admin/Notification.vue';
import Settings from '@/pages/admin/Settings.vue';
import store from '@/store'; // Import the store

const routes = [
  { path: '/', redirect: '/login' }, // Redirect root to login
  { path: '/login', component: Login },
  { path: '/reset-password', component: ResetPassword },
  { path: '/Usermanagement', component: UserManagement, meta: { requiresAuth: true },},
  { path: '/AdminInventory', component: Inventory, meta: { requiresAuth: true },},
  { path: '/AdminTransaction',component: Transaction, meta: { requiresAuth: true },},
  { path: '/ActivityLogs', component: ActivityLogs, meta: { requiresAuth: true }, },
  { path: '/Notification', component: Notification, meta: { requiresAuth: true },},
  { path: '/Settings', component: Settings, meta: { requiresAuth: true },},
  
  
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const isAuthenticated = store.getters["auth/isAuthenticated"];
  const userRole = store.getters["auth/userRole"]; // get the user role

  if (to.path === "/login" && isAuthenticated) {
    // Redirect based on role
    if (userRole === "admin"){
      next("/UserManagement");
    } else if (userRole === "manager"){
      next("/AdminTransaction"); 
    }
      else if (userRole === "warehouse_staff"){
        next("/AdminInventory"); 
    }
    else if (userRole === "procurement"){
        next("/UserManagement");
    }
     else {
      next("/"); // Default fallback if role is unknown
    }

  } else if (to.meta.requiresAuth && !isAuthenticated) {
    next({ path: "/login", query: { redirect: to.fullPath } }); // Save where the user was going
  } else {
    next();
  }
});

export default router;