import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../services/api'

export interface TenantConfig {
  primaryColor: string
  secondaryColor: string
  logoUrl: string
}

export const useTenantStore = defineStore('tenant', () => {
  const tenantId = ref<number | null>(
    localStorage.getItem('tenant_id') ? Number(localStorage.getItem('tenant_id')) : null
  )
  const primaryColor = ref<string>(localStorage.getItem('tenant_primary_color') || '#4f46e5')
  const secondaryColor = ref<string>(localStorage.getItem('tenant_secondary_color') || '#0f172a')
  const logoUrl = ref<string>(localStorage.getItem('tenant_logo_url') || '/logo.png')

  // Apply CSS variables to root element for live white-label styling
  function applyTheme() {
    document.documentElement.style.setProperty('--primary-color', primaryColor.value)
    document.documentElement.style.setProperty('--secondary-color', secondaryColor.value)
  }

  async function fetchBranding(forcedId?: number) {
    try {
      const response = await api.get('/tenant/resolve', {
        params: forcedId ? { tenant_id: forcedId } : {}
      })
      const { id, primaryColor: pColor, secondaryColor: sColor, logoUrl: lUrl } = response.data
      tenantId.value = id
      primaryColor.value = pColor
      secondaryColor.value = sColor
      logoUrl.value = lUrl

      localStorage.setItem('tenant_id', String(id))
      localStorage.setItem('tenant_primary_color', pColor)
      localStorage.setItem('tenant_secondary_color', sColor)
      localStorage.setItem('tenant_logo_url', lUrl)
      applyTheme()
    } catch (e) {
      console.error('Failed to resolve tenant branding', e)
    }
  }

  async function setTenantConfig(config: TenantConfig) {
    primaryColor.value = config.primaryColor
    secondaryColor.value = config.secondaryColor
    logoUrl.value = config.logoUrl

    localStorage.setItem('tenant_primary_color', config.primaryColor)
    localStorage.setItem('tenant_secondary_color', config.secondaryColor)
    localStorage.setItem('tenant_logo_url', config.logoUrl)
    applyTheme()

    try {
      if (tenantId.value) {
        await api.post('/tenant/branding', {
          tenant_id: tenantId.value,
          primaryColor: config.primaryColor,
          secondaryColor: config.secondaryColor,
          logoUrl: config.logoUrl
        })
      }
    } catch (e) {
      console.error('Failed to save branding on backend', e)
    }
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
    tenantId,
    primaryColor,
    secondaryColor,
    logoUrl,
    fetchBranding,
    setTenantConfig,
    resetToDefault,
    applyTheme
  }
})
