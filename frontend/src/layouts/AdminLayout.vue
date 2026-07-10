<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/useAuthStore'
import { useTenantStore } from '../stores/useTenantStore'

const authStore = useAuthStore()
const tenantStore = useTenantStore()
const router = useRouter()

const isSidebarOpen = ref(true)

const userName = computed(() => authStore.user?.name || 'Admin User')

const userInitials = computed(() => {
  const name = userName.value
  const parts = name.split(' ')
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return name.slice(0, 2).toUpperCase()
})

function handleLogout() {
  authStore.clearAuth()
  router.push('/login')
}

function toggleSidebar() {
  isSidebarOpen.value = !isSidebarOpen.value
}
</script>

<template>
  <div class="admin-layout" :class="{ 'sidebar-collapsed': !isSidebarOpen }">
    <!-- Mobile Header -->
    <header class="mobile-header">
      <button @click="toggleSidebar" class="menu-toggle">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="menu-icon">
          <line x1="3" y1="12" x2="21" y2="12"></line>
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
      </button>
      <img :src="tenantStore.logoUrl" alt="Tenant Logo" class="mobile-logo" />
      <div class="mobile-avatar" :style="{ backgroundColor: 'var(--primary-color)' }">
        {{ userInitials }}
      </div>
    </header>

    <!-- Sidebar Wrapper -->
    <aside class="sidebar" :class="{ 'open': isSidebarOpen }">
      <div class="sidebar-top">
        <!-- Logo -->
        <div class="brand">
          <img :src="tenantStore.logoUrl" alt="Tenant Logo" class="brand-logo" />
          <span class="brand-name">Admin</span>
        </div>
        
        <!-- Toggle button inside sidebar -->
        <button @click="toggleSidebar" class="sidebar-toggle-btn" title="Collapse Sidebar">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="chevron-icon">
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
        </button>
      </div>

      <!-- Navigation Links -->
      <nav class="nav-menu">
        <router-link to="/admin/dashboard" class="nav-item" active-class="active">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="nav-icon">
            <rect x="3" y="3" width="7" height="9"></rect>
            <rect x="14" y="3" width="7" height="5"></rect>
            <rect x="14" y="12" width="7" height="9"></rect>
            <rect x="3" y="16" width="7" height="5"></rect>
          </svg>
          <span class="nav-text">Dashboard</span>
        </router-link>

        <router-link to="/admin/employees" class="nav-item" active-class="active">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="nav-icon">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
          <span class="nav-text">Employees</span>
        </router-link>

        <router-link to="/admin/journey-builder" class="nav-item" active-class="active">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="nav-icon">
            <path d="M18 3a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3 3 3 0 0 0 3-3V6a3 3 0 0 0-3-3z"></path>
            <path d="M6 21a3 3 0 0 0 3-3V6a3 3 0 0 0-3-3 3 3 0 0 0-3 3v12a3 3 0 0 0 3 3z"></path>
            <line x1="9" y1="9" x2="15" y2="9"></line>
            <line x1="9" y1="15" x2="15" y2="15"></line>
          </svg>
          <span class="nav-text">Journey Builder</span>
        </router-link>

        <router-link to="/admin/settings" class="nav-item" active-class="active">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="nav-icon">
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
          </svg>
          <span class="nav-text">Settings</span>
        </router-link>
      </nav>

      <!-- Sidebar Footer User Card -->
      <div class="sidebar-footer">
        <div class="user-card">
          <div class="avatar" :style="{ backgroundColor: 'var(--primary-color)' }">
            {{ userInitials }}
          </div>
          <div class="user-info">
            <span class="user-name">{{ userName }}</span>
            <span class="user-role">Administrator</span>
          </div>
        </div>
        <button @click="handleLogout" class="logout-button">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="logout-icon">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
          <span class="logout-text">Log Out</span>
        </button>
      </div>
    </aside>

    <!-- Main Workspace Container -->
    <main class="main-workspace">
      <div class="workspace-content">
        <router-view />
      </div>
    </main>

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div v-if="isSidebarOpen" @click="toggleSidebar" class="sidebar-backdrop"></div>
  </div>
</template>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
  background-color: #0b0c10;
  color: #f1f5f9;
  font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}

/* Sidebar Styling */
.sidebar {
  width: 280px;
  background: rgba(15, 23, 42, 0.95);
  border-right: 1px solid rgba(255, 255, 255, 0.08);
  display: flex;
  flex-direction: column;
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  z-index: 101;
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), width 0.3s ease;
  box-shadow: 10px 0 30px rgba(0, 0, 0, 0.2);
}

.sidebar-top {
  height: 72px;
  padding: 0 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.brand {
  display: flex;
  align-items: center;
  gap: 12px;
  overflow: hidden;
  white-space: nowrap;
}

.brand-logo {
  height: 36px;
  max-width: 120px;
  object-fit: contain;
}

.brand-name {
  font-size: 1.1rem;
  font-weight: 700;
  color: #ffffff;
  letter-spacing: 0.5px;
}

.sidebar-toggle-btn {
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 6px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.sidebar-toggle-btn:hover {
  background: rgba(255, 255, 255, 0.05);
  color: #ffffff;
}

.chevron-icon {
  width: 18px;
  height: 18px;
  transition: transform 0.3s ease;
}

/* Nav Menu */
.nav-menu {
  flex-grow: 1;
  padding: 24px 16px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 12px 16px;
  border-radius: 12px;
  color: #94a3b8;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.95rem;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  border: 1px solid transparent;
}

.nav-item:hover {
  color: #ffffff;
  background: rgba(255, 255, 255, 0.04);
}

.nav-item.active {
  color: #ffffff;
  background: rgba(255, 255, 255, 0.06);
  border-color: rgba(255, 255, 255, 0.1);
  box-shadow: inset 4px 0 0 var(--primary-color);
}

.nav-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

/* Sidebar Footer */
.sidebar-footer {
  padding: 20px;
  border-top: 1px solid rgba(255, 255, 255, 0.05);
  display: flex;
  flex-direction: column;
  gap: 16px;
  background: rgba(10, 16, 30, 0.4);
}

.user-card {
  display: flex;
  align-items: center;
  gap: 12px;
  overflow: hidden;
}

.avatar {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  font-weight: 700;
  flex-shrink: 0;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.user-info {
  display: flex;
  flex-direction: column;
  text-align: left;
  overflow: hidden;
}

.user-name {
  font-size: 0.9rem;
  font-weight: 600;
  color: #ffffff;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-role {
  font-size: 0.75rem;
  color: #94a3b8;
}

.logout-button {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 10px;
  border-radius: 10px;
  background: rgba(239, 68, 68, 0.08);
  border: 1px solid rgba(239, 68, 68, 0.15);
  color: #ef4444;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.logout-button:hover {
  background: rgba(239, 68, 68, 0.2);
  border-color: rgba(239, 68, 68, 0.3);
}

.logout-icon {
  width: 16px;
  height: 16px;
}

/* Main Workspace */
.main-workspace {
  flex-grow: 1;
  margin-left: 280px;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  transition: margin-left 0.3s ease;
}

.workspace-content {
  padding: 40px;
  box-sizing: border-box;
  width: 100%;
  max-width: 1400px;
  margin: 0 auto;
}

/* Collapsed State Styles (Desktop) */
.sidebar-collapsed .sidebar {
  width: 80px;
}

.sidebar-collapsed .sidebar .brand-name,
.sidebar-collapsed .sidebar .nav-text,
.sidebar-collapsed .sidebar .user-info,
.sidebar-collapsed .sidebar .logout-text {
  display: none;
}

.sidebar-collapsed .sidebar .sidebar-top {
  justify-content: center;
  padding: 0;
}

.sidebar-collapsed .sidebar .brand {
  justify-content: center;
}

.sidebar-collapsed .sidebar .sidebar-toggle-btn {
  display: none; /* Can restore or show different button */
}

.sidebar-collapsed .sidebar .nav-item {
  justify-content: center;
  padding: 12px;
}

.sidebar-collapsed .sidebar .user-card {
  justify-content: center;
}

.sidebar-collapsed .sidebar .logout-button {
  padding: 10px;
  justify-content: center;
}

.sidebar-collapsed .main-workspace {
  margin-left: 80px;
}

/* Mobile Header Styles */
.mobile-header {
  display: none;
  height: 64px;
  background-color: #0f172a;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  padding: 0 16px;
  align-items: center;
  justify-content: space-between;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 100;
}

.menu-toggle {
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 6px;
}

.menu-icon {
  width: 24px;
  height: 24px;
}

.mobile-logo {
  height: 32px;
  max-width: 120px;
  object-fit: contain;
}

.mobile-avatar {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
  font-weight: 700;
}

.sidebar-backdrop {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  z-index: 100;
}

@media (max-width: 1024px) {
  .mobile-header {
    display: flex;
  }

  .sidebar {
    transform: translateX(-100%);
    width: 280px !important;
  }

  .sidebar.open {
    transform: translateX(0);
  }

  .sidebar-collapsed .sidebar {
    transform: translateX(-100%);
  }

  .sidebar-toggle-btn {
    display: none;
  }

  .main-workspace {
    margin-left: 0 !important;
    padding-top: 64px;
  }

  .workspace-content {
    padding: 24px 16px;
  }

  .sidebar-backdrop {
    display: block;
  }
}
</style>
