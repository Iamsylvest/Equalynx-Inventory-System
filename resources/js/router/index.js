import { createRouter, createWebHistory } from 'vue-router';
import Login from '@/auth/Login.vue'; // Assuming Login.vue is in auth
import ResetPassword from '@/auth/ResetPassword.vue'; // Correct path for ResetPassword.vue
import UserManagement from '@/pages/admin/UserManagement.vue';

const routes = [
    { path: '/', component: Login },
    { path: '/login', component: Login },  // Explicit /login path
    { path: '/reset-password', component: ResetPassword },
    { path: '/UserManagement', component: UserManagement },
  ];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;