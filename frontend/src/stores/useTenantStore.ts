import { defineStore } from 'pinia'
import { ref } from 'vue'

export interface TenantConfig {
  primaryColor: string
  secondaryColor: string
  logoUrl: string
}

export const useTenantStore = defineStore('tenant', () => {
  // Default values
  const primaryColor = ref<string>(localStorage.getItem('tenant_primary_color') || '#4f46e5')
  const secondaryColor = ref<string>(localStorage.getItem('tenant_secondary_color') || '#0f172a')
  const logoUrl = ref<string>(localStorage.getItem('tenant_logo_url') || '/logo.png')

  // Apply CSS variables to root element for live white-label styling
  function applyTheme() {
    document.documentElement.style.setProperty('--primary-color', primaryColor.value)
    document.documentElement.style.setProperty('--secondary-color', secondaryColor.value)
  }

  function setTenantConfig(config: TenantConfig) {
    primaryColor.value = config.primaryColor
    secondaryColor.value = config.secondaryColor
    logoUrl.value = config.logoUrl

    localStorage.setItem('tenant_primary_color', config.primaryColor)
    localStorage.setItem('tenant_secondary_color', config.secondaryColor)
    localStorage.setItem('tenant_logo_url', config.logoUrl)

    applyTheme()
  }

  function resetToDefault() {
    primaryColor.value = '#4f46e5'
    secondaryColor.value = '#0f172a'
    logoUrl.value = '/logo.png'

    localStorage.removeItem('tenant_primary_color')
    localStorage.removeItem('tenant_secondary_color')
    localStorage.removeItem('tenant_logo_url')

    applyTheme()
  }

  // Initial call to ensure colors are applied when store is initialized
  applyTheme()

  return {
    primaryColor,
    secondaryColor,
    logoUrl,
    setTenantConfig,
    resetToDefault,
    applyTheme
  }
})
