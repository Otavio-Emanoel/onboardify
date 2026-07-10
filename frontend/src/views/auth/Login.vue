<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore, type User, type UserRole } from '../../stores/useAuthStore'

const authStore = useAuthStore()
const router = useRouter()

const email = ref('')
const password = ref('')
const errorMsg = ref('')
const isLoading = ref(false)

async function handleLogin() {
  if (!email.value || !password.value) {
    errorMsg.value = 'Please enter both email and password.'
    return
  }

  errorMsg.value = ''
  isLoading.value = true

  try {
    // Simulate hitting a backend API with 800ms delay
    await new Promise((resolve) => setTimeout(resolve, 800))

    let user: User
    let role: UserRole
    let token = 'mock_jwt_token_xyz'

    const trimmedEmail = email.value.trim().toLowerCase()

    if (trimmedEmail === 'admin@onboardify.com') {
      role = 'admin'
      user = {
        id: 'usr_admin_001',
        name: 'Jane Doe',
        email: trimmedEmail
      }
    } else if (trimmedEmail === 'employee@onboardify.com' || trimmedEmail.endsWith('@employee.com')) {
      role = 'employee'
      user = {
        id: 'usr_emp_101',
        name: 'Alex Mercer',
        email: trimmedEmail,
        level: 1,
        points: 120
      }
    } else {
      // Default fallback for any other email for demo purposes
      role = 'employee'
      user = {
        id: 'usr_emp_fallback',
        name: email.value.split('@')[0],
        email: trimmedEmail,
        level: 1,
        points: 0
      }
    }

    // Save mock state to auth store
    authStore.setAuth(user, role, token)

    // Redirect to correct dashboard layout
    if (role === 'admin') {
      router.push('/admin/dashboard')
    } else {
      router.push('/dashboard')
    }
  } catch (error: any) {
    errorMsg.value = error.message || 'Login failed. Please try again.'
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="login-container">
    <div class="auth-header">
      <h1 class="auth-title">Log In</h1>
      <p class="auth-subtitle">Welcome back! Please enter your details.</p>
    </div>

    <!-- Error Alert Banner -->
    <div v-if="errorMsg" class="error-banner">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="error-icon">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="8" x2="12" y2="12"></line>
        <line x1="12" y1="16" x2="12.01" y2="16"></line>
      </svg>
      <span>{{ errorMsg }}</span>
    </div>

    <form @submit.prevent="handleLogin" class="auth-form">
      <div class="input-group">
        <label for="email" class="input-label">Email Address</label>
        <input 
          id="email" 
          type="email" 
          v-model="email" 
          placeholder="name@company.com" 
          class="auth-input" 
          required 
          autocomplete="email"
        />
      </div>

      <div class="input-group">
        <label for="password" class="input-label">Password</label>
        <input 
          id="password" 
          type="password" 
          v-model="password" 
          placeholder="••••••••" 
          class="auth-input" 
          required 
          autocomplete="current-password"
        />
      </div>

      <button type="submit" :disabled="isLoading" class="submit-btn">
        <span v-if="isLoading" class="spinner"></span>
        <span v-else>Log In</span>
      </button>
    </form>

    <div class="demo-helpers">
      <p class="demo-label">💡 Demo credentials (any password):</p>
      <div class="demo-role">
        <span>Admin:</span> <code>admin@onboardify.com</code>
      </div>
      <div class="demo-role">
        <span>Employee:</span> <code>employee@onboardify.com</code>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-container {
  display: flex;
  flex-direction: column;
  align-items: stretch;
}

.auth-header {
  margin-bottom: 24px;
  text-align: left;
}

.auth-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: #ffffff;
  margin: 0 0 8px 0;
  letter-spacing: -0.5px;
}

.auth-subtitle {
  font-size: 0.95rem;
  color: #94a3b8;
  margin: 0;
}

.error-banner {
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.2);
  color: #f87171;
  padding: 12px 16px;
  border-radius: 12px;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
  animation: shake 0.4s ease-in-out;
}

.error-icon {
  width: 18px;
  height: 18px;
  flex-shrink: 0;
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  text-align: left;
}

.input-label {
  font-size: 0.85rem;
  font-weight: 600;
  color: #cbd5e1;
}

.auth-input {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  padding: 14px 16px;
  color: #ffffff;
  font-size: 0.95rem;
  outline: none;
  transition: all 0.2s ease;
}

.auth-input:focus {
  border-color: var(--primary-color);
  background: rgba(255, 255, 255, 0.08);
  box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
}

.submit-btn {
  background: var(--primary-color);
  border: none;
  border-radius: 12px;
  padding: 14px;
  color: #ffffff;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 10px;
  box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
}

.submit-btn:hover {
  transform: translateY(-1px);
  filter: brightness(1.1);
  box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
}

.submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.spinner {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #ffffff;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.demo-helpers {
  margin-top: 32px;
  padding-top: 24px;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  text-align: left;
}

.demo-label {
  font-size: 0.8rem;
  color: #94a3b8;
  margin: 0 0 8px 0;
  font-weight: 600;
}

.demo-role {
  font-size: 0.8rem;
  color: #cbd5e1;
  margin-bottom: 6px;
}

.demo-role span {
  font-weight: 500;
  display: inline-block;
  width: 80px;
}

.demo-role code {
  background: rgba(255, 255, 255, 0.06);
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 0.8rem;
  border: 1px solid rgba(255, 255, 255, 0.08);
  color: #38bdf8;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-4px); }
  75% { transform: translateX(4px); }
}
</style>
