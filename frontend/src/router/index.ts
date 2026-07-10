import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/useAuthStore'

// Layouts
import AuthLayout from '../layouts/AuthLayout.vue'
import EmployeeLayout from '../layouts/EmployeeLayout.vue'
import AdminLayout from '../layouts/AdminLayout.vue'

// Views
import LoginView from '../views/auth/LoginView.vue'
import EmployeeDashboardView from '../views/employee/DashboardView.vue'
import AdminDashboardView from '../views/admin/DashboardView.vue'
import AdminEmployeesView from '../views/admin/EmployeesView.vue'
import AdminJourneyBuilderView from '../views/admin/JourneyBuilderView.vue'
import AdminSettingsView from '../views/admin/SettingsView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // Auth Routes (Login)
    {
      path: '/',
      component: AuthLayout,
      children: [
        {
          path: '',
          redirect: '/login'
        },
        {
          path: 'login',
          name: 'login',
          component: LoginView
        }
      ]
    },
    // Employee Routes
    {
      path: '/dashboard',
      component: EmployeeLayout,
      meta: { requiresAuth: true, role: 'employee' },
      children: [
        {
          path: '',
          name: 'employee-dashboard',
          component: EmployeeDashboardView
        }
      ]
    },
    // Admin Routes
    {
      path: '/admin',
      component: AdminLayout,
      meta: { requiresAuth: true, role: 'admin' },
      children: [
        {
          path: '',
          redirect: '/admin/dashboard'
        },
        {
          path: 'dashboard',
          name: 'admin-dashboard',
          component: AdminDashboardView
        },
        {
          path: 'employees',
          name: 'admin-employees',
          component: AdminEmployeesView
        },
        {
          path: 'journey-builder',
          name: 'admin-journey-builder',
          component: AdminJourneyBuilderView
        },
        {
          path: 'settings',
          name: 'admin-settings',
          component: AdminSettingsView
        }
      ]
    },
    // Catch all undefined routes and redirect to login/dashboard
    {
      path: '/:pathMatch(.*)*',
      redirect: '/login'
    }
  ]
})

// Global Navigation Guard
router.beforeEach((to, _from, next) => {
  const authStore = useAuthStore()

  const isAuthenticated = authStore.isAuthenticated
  const userRole = authStore.role

  // 1. If path is /login and they are already authenticated, redirect to their home
  if (to.path === '/login' && isAuthenticated) {
    if (userRole === 'admin') {
      return next('/admin/dashboard')
    } else {
      return next('/dashboard')
    }
  }

  // 2. Role-based redirect: If employee and route starts with /admin, instantly redirect to /dashboard
  if (userRole === 'employee' && to.path.startsWith('/admin')) {
    return next('/dashboard')
  }

  // 3. Protect authenticated routes
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!isAuthenticated) {
      // Not authenticated, redirect to login
      return next({ name: 'login', query: { redirect: to.fullPath } })
    }

    // Role-specific route protection:
    // e.g., if page requires 'admin' role but user role is not 'admin'
    const requiredRole = to.matched.find(record => record.meta.role)?.meta.role
    if (requiredRole && requiredRole !== userRole) {
      if (userRole === 'admin') {
        return next('/admin/dashboard')
      } else {
        return next('/dashboard')
      }
    }
  }

  next()
})

export default router
