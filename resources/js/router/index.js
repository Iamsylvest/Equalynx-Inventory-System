import { createRouter, createWebHistory } from 'vue-router';
import Login from '@/auth/Login.vue';

import ResetPassword from '@/auth/ResetPassword.vue';
import UserManagement from '@/pages/admin/UserManagement.vue'; 
import Inventory from '@/pages/admin/Inventory.vue';
import TransactionTrail from '@/pages/admin/TransactionTrail.vue';
import Transaction from '@/pages/admin/Transaction.vue';
import ActivityLogs from '@/pages/admin/ActivityLogs.vue';
import Notification from '@/pages/admin/Notification.vue';
import Settings from '@/pages/admin/Settings.vue';
import ArchivePage from '@/pages/admin/ArchivePage.vue';
import ProcurementDashboard from '@/pages/admin/Procurement.vue';
import store from '@/store'; // Import the store

const routes = [
  { path: '/', redirect: '/login' }, 
  { path: '/login', component: Login },
  { path: '/reset-password', component: ResetPassword },
  { path: '/UserManagement', component: UserManagement, meta: { requiresAuth: true }},
  { path: '/TransactionTrail', component: TransactionTrail, meta: { requiresAuth: true }},
  { path: '/procurement', component: ProcurementDashboard, meta: { requiresAuth: true } },
  { path: '/AdminInventory', component: Inventory, meta: { requiresAuth: true }},
  { path: '/AdminTransaction', component: Transaction, meta: { requiresAuth: true }},
  { path: '/ActivityLogs', component: ActivityLogs, meta: { requiresAuth: true }},
  { path: '/Notification', component: Notification, meta: { requiresAuth: true }},
  { path: '/Archived', component: ArchivePage, meta: { requiresAuth: true }},
  { path: '/Settings', component: Settings, meta: { requiresAuth: true }},
  // Catch-all route for undefined routes
  { path: '/:pathMatch(.*)*', redirect: '/login' },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const isAuthenticated = store.getters["auth/isAuthenticated"];
  const userRole = store.getters["auth/userRole"];

  if (to.path === "/login" && isAuthenticated) {
    if (userRole === "admin") {
      next("/UserManagement");
    } else if (userRole === "manager") {
      next("/AdminTransaction");
    } else if (userRole === "warehouse_staff") {
      next("/AdminInventory");
    } else if (userRole === "procurement") {
      next("/procurement");
    } else {
      next("/"); // Fallback for unknown roles
    }
  } else if (to.meta.requiresAuth && !isAuthenticated) {
    next({ path: "/login", query: { redirect: to.fullPath } }); 
  } else {
    next(); // Continue if no issues
  }
});

export default router;