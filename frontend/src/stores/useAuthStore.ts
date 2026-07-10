import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../services/api'

export interface User {
  id: string
  name: string
  email: string
  level?: number
  points?: number
  tenant_id?: number
}

export type UserRole = 'admin' | 'employee'

export const useAuthStore = defineStore('auth', () => {
  // State initialized from localStorage if available
  const user = ref<User | null>(
    localStorage.getItem('auth_user') 
      ? JSON.parse(localStorage.getItem('auth_user') as string) 
      : null
  )
  
  const role = ref<UserRole | null>(
    (localStorage.getItem('auth_role') as UserRole) || null
  )
  
  const token = ref<string | null>(
    localStorage.getItem('auth_token') || null
  )

  // Getters
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isAdmin = computed(() => role.value === 'admin')
  const isEmployee = computed(() => role.value === 'employee')

  // Actions
  function setAuth(newUser: User, newRole: UserRole, newToken: string) {
    user.value = newUser
    role.value = newRole
    token.value = newToken

    localStorage.setItem('auth_user', JSON.stringify(newUser))
    localStorage.setItem('auth_role', newRole)
    localStorage.setItem('auth_token', newToken)
  }

  function clearAuth() {
    user.value = null
    role.value = null
    token.value = null

    localStorage.removeItem('auth_user')
    localStorage.removeItem('auth_role')
    localStorage.removeItem('auth_token')
  }

  async function login(emailVal: string, passwordVal: string) {
    const response = await api.post('/auth/login', {
      email: emailVal,
      password: passwordVal
    })
    const { token: tokenVal, user: userVal } = response.data
    setAuth(userVal, userVal.role, tokenVal)
  }

  async function acceptInvite(passwordVal: string, confirmPasswordVal: string) {
    const response = await api.post('/auth/invite/accept', {
      password: passwordVal,
      password_confirmation: confirmPasswordVal
    })
    const { token: tokenVal, user: userVal } = response.data
    setAuth(userVal, userVal.role, tokenVal)
  }

  async function logout() {
    try {
      await api.post('/auth/logout')
    } catch (e) {
      // Ignore network errors on logout
    } finally {
      clearAuth()
    }
  }

  function addPoints(pts: number) {
    if (user.value) {
      const currentPoints = user.value.points || 0
      const newPoints = currentPoints + pts
      user.value.points = newPoints
      // Every 100 points represents a level up!
      user.value.level = Math.floor(newPoints / 100) + 1

      localStorage.setItem('auth_user', JSON.stringify(user.value))
    }
  }

  return {
    user,
    role,
    token,
    isAuthenticated,
    isAdmin,
    isEmployee,
    setAuth,
    clearAuth,
    login,
    acceptInvite,
    logout,
    addPoints
  }
})
