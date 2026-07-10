import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export interface User {
  id: string
  name: string
  email: string
  level?: number
  points?: number
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

  return {
    user,
    role,
    token,
    isAuthenticated,
    isAdmin,
    isEmployee,
    setAuth,
    clearAuth
  }
})
