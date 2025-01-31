import { createRouter, createWebHistory } from 'vue-router';
import Login from '@/auth/Login.vue';
import ResetPassword from '@/auth/ResetPassword.vue';
import UserManagement from '@/pages/admin/UserManagement.vue';
import Inventory from '@/pages/admin/Inventory.vue';
import Transaction from '@/pages/admin/Transaction.vue';
import store from '@/store'; // Import the store

const routes = [
  { path: '/', redirect: '/login' }, // Redirect root to login
  { path: '/login', component: Login },
  { path: '/reset-password', component: ResetPassword },
  {
    path: '/Usermanagement',
    component: UserManagement,
    meta: { requiresAuth: true },
  },
  {
    path: '/AdminInventory',
    component: Inventory,
    meta: { requiresAuth: true },
  },
  {
    path: '/AdminTransaction',
    component: Transaction,
    meta: { requiresAuth: true },
  },
  
  
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const isAuthenticated = store.getters["auth/isAuthenticated"];

  if (to.path === "/login" && isAuthenticated) {
    next("/UserManagement"); // Redirect logged-in users to dashboard
  } else if (to.meta.requiresAuth && !isAuthenticated) {
    next({ path: "/login", query: { redirect: to.fullPath } }); // Save where the user was going
  } else {
    next();
  }
});

export default router;