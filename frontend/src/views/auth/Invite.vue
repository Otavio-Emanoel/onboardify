<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore, type User } from '../../stores/useAuthStore'

const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()

const token = ref('')
const password = ref('')
const confirmPassword = ref('')
const errorMsg = ref('')
const successMsg = ref('')
const isLoading = ref(false)

onMounted(() => {
  // Extract token from query params or route params
  const queryToken = route.query.token as string
  const routeToken = route.params.token as string
  token.value = queryToken || routeToken || 'demo_invite_token'
})

async function handleAcceptInvite() {
  if (!password.value || !confirmPassword.value) {
    errorMsg.value = 'Please complete all fields.'
    return
  }

  if (password.value !== confirmPassword.value) {
    errorMsg.value = 'Passwords do not match.'
    return
  }

  if (password.value.length < 8) {
    errorMsg.value = 'Password must be at least 8 characters long.'
    return
  }

  errorMsg.value = ''
  isLoading.value = true

  try {
    // Simulate API request to activate account with the new password
    await new Promise((resolve) => setTimeout(resolve, 1000))

    // Automatically log the new hire in for seamless onboarding
    const mockUser: User = {
      id: 'usr_new_hire_' + Math.floor(Math.random() * 1000),
      name: 'New Hire',
      email: 'newhire@company.com',
      level: 1,
      points: 0
    }
    const mockToken = 'mock_jwt_token_new_hire_' + token.value

    authStore.setAuth(mockUser, 'employee', mockToken)
    
    successMsg.value = 'Account activated successfully! Starting your onboarding journey...'
    
    // Redirect to employee dashboard after 1.5s
    setTimeout(() => {
      router.push('/dashboard')
    }, 1500)

  } catch (error: any) {
    errorMsg.value = error.message || 'Something went wrong. Please request a new invitation link.'
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="invite-container">
    <div class="auth-header">
      <h1 class="auth-title">Welcome Aboard!</h1>
      <p class="auth-subtitle">Set up your password to start your onboarding journey.</p>
    </div>

    <!-- Success Message Banner -->
    <div v-if="successMsg" class="success-banner">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="success-icon">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
        <polyline points="22 4 12 14.01 9 11.01"></polyline>
      </svg>
      <span>{{ successMsg }}</span>
    </div>

    <!-- Error Message Banner -->
    <div v-if="errorMsg" class="error-banner">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="error-icon">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="8" x2="12" y2="12"></line>
        <line x1="12" y1="16" x2="12.01" y2="16"></line>
      </svg>
      <span>{{ errorMsg }}</span>
    </div>

    <form v-if="!successMsg" @submit.prevent="handleAcceptInvite" class="auth-form">
      <div class="input-group">
        <label for="password" class="input-label">New Password</label>
        <input 
          id="password" 
          type="password" 
          v-model="password" 
          placeholder="Min. 8 characters" 
          class="auth-input" 
          required 
          autocomplete="new-password"
        />
      </div>

      <div class="input-group">
        <label for="confirm-password" class="input-label">Confirm Password</label>
        <input 
          id="confirm-password" 
          type="password" 
          v-model="confirmPassword" 
          placeholder="Repeat password" 
          class="auth-input" 
          required 
          autocomplete="new-password"
        />
      </div>

      <button type="submit" :disabled="isLoading" class="submit-btn">
        <span v-if="isLoading" class="spinner"></span>
        <span v-else>Start Onboarding</span>
      </button>
    </form>
  </div>
</template>

<style scoped>
.invite-container {
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

.success-banner {
  background: rgba(16, 185, 129, 0.1);
  border: 1px solid rgba(16, 185, 129, 0.25);
  color: #34d399;
  padding: 14px 16px;
  border-radius: 12px;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
  line-height: 1.4;
  text-align: left;
}

.success-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
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
  text-align: left;
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

@keyframes spin {
  to { transform: rotate(360deg); }
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-4px); }
  75% { transform: translateX(4px); }
}
</style>
