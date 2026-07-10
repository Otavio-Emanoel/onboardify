<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/useAuthStore'
import { useTenantStore } from '../stores/useTenantStore'

const authStore = useAuthStore()
const tenantStore = useTenantStore()
const router = useRouter()

// Get user info with defaults
const userName = computed(() => authStore.user?.name || 'Guest Employee')
const userLevel = computed(() => authStore.user?.level ?? 1)
const userPoints = computed(() => authStore.user?.points ?? 0)

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
</script>

<template>
  <div class="employee-layout">
    <!-- Sticky Top Navbar -->
    <header class="navbar">
      <div class="navbar-container">
        <!-- Brand/Logo -->
        <div class="brand">
          <img :src="tenantStore.logoUrl" alt="Tenant Logo" class="brand-logo" />
          <span class="brand-name">Portal</span>
        </div>

        <!-- User Info / Stats -->
        <div class="nav-right">
          <div class="stats-container">
            <div class="stat-badge level-badge">
              <span class="stat-label">Level</span>
              <span class="stat-val">{{ userLevel }}</span>
            </div>
            
            <div class="stat-badge points-badge">
              <span class="stat-label">Points</span>
              <span class="stat-val font-mono">{{ userPoints }}</span>
            </div>
          </div>

          <div class="user-profile">
            <div class="avatar" :style="{ backgroundColor: 'var(--primary-color)' }">
              {{ userInitials }}
            </div>
            <div class="user-details">
              <span class="user-name">{{ userName }}</span>
              <span class="user-role">Employee</span>
            </div>
            <button @click="handleLogout" class="logout-btn" title="Log Out">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="logout-icon">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content Panel -->
    <main class="main-content">
      <div class="content-container">
        <router-view />
      </div>
    </main>
  </div>
</template>

<style scoped>
.employee-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: #0b0c10;
  color: #e2e8f0;
  font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}

.navbar {
  position: sticky;
  top: 0;
  z-index: 100;
  background: rgba(15, 23, 42, 0.65);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
}

.navbar-container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 24px;
  height: 72px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.brand {
  display: flex;
  align-items: center;
  gap: 12px;
}

.brand-logo {
  height: 38px;
  max-width: 140px;
  object-fit: contain;
}

.brand-name {
  font-size: 1.1rem;
  font-weight: 600;
  letter-spacing: 0.5px;
  background: linear-gradient(135deg, #ffffff 0%, #94a3b8 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.nav-right {
  display: flex;
  align-items: center;
  gap: 24px;
}

.stats-container {
  display: flex;
  align-items: center;
  gap: 12px;
}

.stat-badge {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 6px 14px;
  border-radius: 9999px;
  font-size: 0.85rem;
  font-weight: 600;
  border: 1px solid rgba(255, 255, 255, 0.08);
  background: rgba(255, 255, 255, 0.03);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

.level-badge {
  color: #38bdf8;
}

.level-badge .stat-val {
  background: rgba(56, 189, 248, 0.15);
  padding: 2px 8px;
  border-radius: 9999px;
  border: 1px solid rgba(56, 189, 248, 0.2);
}

.points-badge {
  color: #fbbf24;
}

.points-badge .stat-val {
  background: rgba(251, 191, 36, 0.15);
  padding: 2px 8px;
  border-radius: 9999px;
  border: 1px solid rgba(251, 191, 36, 0.2);
}

.user-profile {
  display: flex;
  align-items: center;
  gap: 12px;
  border-left: 1px solid rgba(255, 255, 255, 0.12);
  padding-left: 20px;
}

.avatar {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
  font-weight: 700;
  border: 2px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.user-details {
  display: flex;
  flex-direction: column;
  text-align: left;
}

.user-name {
  font-size: 0.9rem;
  font-weight: 600;
  color: #f1f5f9;
  max-width: 140px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-role {
  font-size: 0.75rem;
  color: #94a3b8;
}

.logout-btn {
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 6px;
  border-radius: 8px;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logout-btn:hover {
  color: #ef4444;
  background: rgba(239, 68, 68, 0.15);
}

.logout-icon {
  width: 18px;
  height: 18px;
}

.main-content {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.content-container {
  max-width: 1280px;
  width: 100%;
  margin: 0 auto;
  padding: 32px 24px;
  box-sizing: border-box;
  flex-grow: 1;
}

@media (max-width: 768px) {
  .navbar-container {
    padding: 0 16px;
    height: auto;
    padding-top: 12px;
    padding-bottom: 12px;
    flex-direction: column;
    gap: 12px;
  }
  .nav-right {
    width: 100%;
    justify-content: space-between;
  }
  .content-container {
    padding: 20px 16px;
  }
}
</style>
