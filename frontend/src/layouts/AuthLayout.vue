<script setup lang="ts">
import { useTenantStore } from '../stores/useTenantStore'

const tenantStore = useTenantStore()
</script>

<template>
  <div class="auth-layout">
    <div class="auth-card">
      <div class="logo-container">
        <img :src="tenantStore.logoUrl" alt="Company Logo" class="tenant-logo" />
      </div>
      <div class="auth-content">
        <router-view />
      </div>
    </div>
  </div>
</template>

<style scoped>
.auth-layout {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: radial-gradient(circle at 10% 20%, var(--secondary-color) 0%, #0d0e12 100%);
  padding: 24px;
  box-sizing: border-box;
  font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}

.auth-card {
  width: 100%;
  max-width: 440px;
  background: rgba(255, 255, 255, 0.03);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  padding: 40px 32px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 
              inset 0 1px 0 rgba(255, 255, 255, 0.1);
  display: flex;
  flex-direction: column;
  align-items: stretch;
  animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.logo-container {
  display: flex;
  justify-content: center;
  margin-bottom: 32px;
}

.tenant-logo {
  max-height: 56px;
  max-width: 240px;
  object-fit: contain;
  filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.2));
  transition: transform 0.3s ease;
}

.tenant-logo:hover {
  transform: scale(1.02);
}

.auth-content {
  flex-grow: 1;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(12px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 480px) {
  .auth-card {
    padding: 32px 20px;
    border-radius: 16px;
  }
}
</style>
