<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useTenantStore } from '../../stores/useTenantStore'

const tenantStore = useTenantStore()

const primaryColor = ref('#4f46e5')
const secondaryColor = ref('#0f172a')
const logoUrl = ref('/logo.png')
const showSuccessMsg = ref(false)

onMounted(() => {
  primaryColor.value = tenantStore.primaryColor
  secondaryColor.value = tenantStore.secondaryColor
  logoUrl.value = tenantStore.logoUrl
})

function handleLogoUpload(event: Event) {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]
    const reader = new FileReader()
    
    reader.onload = (e) => {
      if (e.target?.result) {
        logoUrl.value = e.target.result as string
        // Apply instantly to preview
      }
    }
    reader.readAsDataURL(file)
  }
}

async function saveBranding() {
  await tenantStore.setTenantConfig({
    primaryColor: primaryColor.value,
    secondaryColor: secondaryColor.value,
    logoUrl: logoUrl.value
  })

  showSuccessMsg.value = true
  setTimeout(() => {
    showSuccessMsg.value = false
  }, 3000)
}

function resetToDefault() {
  tenantStore.resetToDefault()
  primaryColor.value = tenantStore.primaryColor
  secondaryColor.value = tenantStore.secondaryColor
  logoUrl.value = tenantStore.logoUrl

  showSuccessMsg.value = true
  setTimeout(() => {
    showSuccessMsg.value = false
  }, 3000)
}
</script>

<template>
  <div class="settings-wrapper">
    <div class="settings-header">
      <h1 class="page-title">Branding Settings</h1>
      <p class="page-subtitle">White-label your onboarding portal. Customize colors and upload your company logo.</p>
    </div>

    <!-- Success Message Banner -->
    <div v-if="showSuccessMsg" class="success-banner animate-slide-in">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="success-icon">
        <polyline points="20 6 9 17 4 12"></polyline>
      </svg>
      <span>Branding configurations updated and applied globally!</span>
    </div>

    <div class="settings-grid">
      <!-- Left side: Configuration Form -->
      <div class="form-card">
        <h2 class="card-title">Portal Theme Customizer</h2>
        
        <form @submit.prevent="saveBranding" class="branding-form">
          <!-- Logo Selection -->
          <div class="form-section">
            <h3 class="section-heading">Company Logo</h3>
            <div class="logo-selector-block">
              <div class="logo-preview-box">
                <img :src="logoUrl" alt="Logo Preview" class="preview-img" />
              </div>
              <div class="logo-upload-controls">
                <label class="upload-btn" :style="{ backgroundColor: 'var(--primary-color)' }">
                  <span>Upload Logo File</span>
                  <input type="file" @change="handleLogoUpload" accept="image/*" class="file-input-hidden" />
                </label>
                <div class="url-input-group">
                  <span class="url-label">Or Logo URL:</span>
                  <input type="text" v-model="logoUrl" class="url-text-input" placeholder="/logo.png" />
                </div>
              </div>
            </div>
          </div>

          <!-- Color Pickers -->
          <div class="form-section">
            <h3 class="section-heading">Brand Color Scheme</h3>
            <div class="colors-row">
              <div class="color-picker-group">
                <label class="color-label">Primary Brand Color</label>
                <div class="color-picker-wrapper">
                  <input type="color" v-model="primaryColor" class="color-picker-input" />
                  <input type="text" v-model="primaryColor" class="color-hex-text font-mono" />
                </div>
                <span class="color-help">Used for primary buttons, active links, and highlights.</span>
              </div>

              <div class="color-picker-group">
                <label class="color-label">Secondary Brand Color</label>
                <div class="color-picker-wrapper">
                  <input type="color" v-model="secondaryColor" class="color-picker-input" />
                  <input type="text" v-model="secondaryColor" class="color-hex-text font-mono" />
                </div>
                <span class="color-help">Used for portal background overlays, cards, and sidebars.</span>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="form-actions">
            <button type="button" @click="resetToDefault" class="reset-btn">
              Reset to Defaults
            </button>
            <button type="submit" class="save-btn" :style="{ backgroundColor: 'var(--primary-color)' }">
              Save & Apply Configuration
            </button>
          </div>
        </form>
      </div>

      <!-- Right side: Realtime Preview Frame -->
      <div class="preview-card">
        <h2 class="card-title">Live Branding Preview</h2>
        <p class="card-subtitle">Inspect your white-label style in real time:</p>
        
        <div class="mock-portal-frame" :style="{ '--mock-primary': primaryColor, '--mock-secondary': secondaryColor }">
          <!-- Mock Header -->
          <div class="mock-navbar" :style="{ background: secondaryColor }">
            <img :src="logoUrl" alt="Brand Logo" class="mock-logo" />
            <div class="mock-stats">
              <span class="mock-badge" :style="{ color: primaryColor, background: 'rgba(255,255,255,0.06)' }">Level 2</span>
              <span class="mock-avatar" :style="{ background: primaryColor }">JD</span>
            </div>
          </div>

          <!-- Mock Body -->
          <div class="mock-body">
            <h4 class="mock-title">Dashboard Spotlight</h4>
            <div class="mock-card">
              <div class="mock-card-line"></div>
              <p class="mock-text">Welcome back! Review your active onboarding roadmap goals.</p>
              
              <div class="mock-progress-container">
                <div class="mock-progress-bar" :style="{ width: '60%', background: primaryColor }"></div>
              </div>
              
              <button class="mock-btn" :style="{ background: primaryColor }">
                Resume Onboarding
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.settings-wrapper {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.settings-header {
  text-align: left;
}

.page-title {
  font-size: 2.2rem;
  font-weight: 700;
  margin: 0 0 8px 0;
  letter-spacing: -0.8px;
}

.page-subtitle {
  font-size: 1.05rem;
  color: #94a3b8;
  max-width: 720px;
  line-height: 1.6;
}

.success-banner {
  background: rgba(16, 185, 129, 0.1);
  border: 1px solid rgba(16, 185, 129, 0.25);
  color: #34d399;
  padding: 14px 20px;
  border-radius: 16px;
  font-size: 0.95rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 12px;
  text-align: left;
}

.success-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

/* Grid Layout */
.settings-grid {
  display: grid;
  grid-template-columns: 1.5fr 1fr;
  gap: 32px;
  align-items: start;
}

.form-card {
  background: rgba(15, 23, 42, 0.5);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  padding: 32px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  text-align: left;
}

.card-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: #ffffff;
  margin: 0 0 28px 0;
}

.branding-form {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.form-section {
  display: flex;
  flex-direction: column;
  gap: 16px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  padding-bottom: 28px;
}

.section-heading {
  font-size: 1rem;
  font-weight: 700;
  color: #ffffff;
  margin: 0;
}

/* Logo Upload Block */
.logo-selector-block {
  display: flex;
  align-items: center;
  gap: 24px;
}

.logo-preview-box {
  width: 100px;
  height: 100px;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.08);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  padding: 12px;
  flex-shrink: 0;
}

.preview-img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.logo-upload-controls {
  display: flex;
  flex-direction: column;
  gap: 12px;
  flex-grow: 1;
}

.upload-btn {
  border: none;
  color: white;
  border-radius: 10px;
  padding: 10px 18px;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  align-self: flex-start;
  transition: opacity 0.2s ease;
}

.upload-btn:hover {
  opacity: 0.9;
}

.file-input-hidden {
  display: none;
}

.url-input-group {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
}

.url-label {
  font-size: 0.8rem;
  color: #cbd5e1;
  font-weight: 500;
  flex-shrink: 0;
}

.url-text-input {
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 8px;
  padding: 8px 12px;
  color: white;
  outline: none;
  font-size: 0.85rem;
  flex-grow: 1;
  transition: all 0.2s ease;
}

.url-text-input:focus {
  border-color: var(--primary-color);
  background: rgba(255, 255, 255, 0.08);
}

/* Colors Customization */
.colors-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
}

.color-picker-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.color-label {
  font-size: 0.85rem;
  font-weight: 600;
  color: #cbd5e1;
}

.color-picker-wrapper {
  display: flex;
  align-items: center;
  gap: 12px;
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 10px;
  padding: 6px 12px;
}

.color-picker-input {
  background: none;
  border: none;
  width: 38px;
  height: 38px;
  padding: 0;
  cursor: pointer;
}

.color-hex-text {
  background: none;
  border: none;
  color: white;
  outline: none;
  font-size: 0.9rem;
  width: 100%;
}

.color-help {
  font-size: 0.75rem;
  color: #94a3b8;
  line-height: 1.4;
}

/* Form Actions */
.form-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 12px;
}

.reset-btn {
  background: none;
  border: 1px solid rgba(255, 255, 255, 0.12);
  color: #cbd5e1;
  padding: 12px 24px;
  border-radius: 12px;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.reset-btn:hover {
  background: rgba(255, 255, 255, 0.05);
  color: white;
}

.save-btn {
  border: none;
  color: white;
  border-radius: 12px;
  padding: 12px 28px;
  font-size: 0.9rem;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 4px 14px rgba(79, 70, 229, 0.25);
  transition: all 0.2s ease;
}

.save-btn:hover {
  filter: brightness(1.1);
  transform: translateY(-1px);
}

/* Live Preview Side Column */
.preview-card {
  background: rgba(15, 23, 42, 0.35);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  padding: 28px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
  text-align: left;
  position: sticky;
  top: 96px;
}

.card-subtitle {
  font-size: 0.85rem;
  color: #94a3b8;
  margin: -20px 0 20px 0;
}

/* Mock Portal Styling */
.mock-portal-frame {
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  background: #020617;
  overflow: hidden;
  box-shadow: 0 12px 28px rgba(0,0,0,0.3);
  display: flex;
  flex-direction: column;
  min-height: 320px;
}

.mock-navbar {
  height: 56px;
  padding: 0 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  transition: background 0.3s ease;
}

.mock-logo {
  height: 24px;
  max-width: 80px;
  object-fit: contain;
}

.mock-stats {
  display: flex;
  align-items: center;
  gap: 8px;
}

.mock-badge {
  font-size: 0.7rem;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: 4px;
}

.mock-avatar {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  color: white;
  font-size: 0.7rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
}

.mock-body {
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.mock-title {
  font-size: 0.85rem;
  font-weight: 700;
  color: white;
  margin: 0;
}

.mock-card {
  background: rgba(255, 255, 255, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 12px;
  padding: 16px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 12px;
}

.mock-card-line {
  width: 32px;
  height: 4px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 2px;
}

.mock-text {
  font-size: 0.75rem;
  color: #cbd5e1;
  margin: 0;
  line-height: 1.4;
}

.mock-progress-container {
  width: 100%;
  height: 4px;
  background: rgba(255, 255, 255, 0.06);
  border-radius: 9999px;
  overflow: hidden;
}

.mock-progress-bar {
  height: 100%;
}

.mock-btn {
  border: none;
  color: white;
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: 6px;
  padding: 6px 12px;
  cursor: pointer;
}

@keyframes slide-in {
  from { opacity: 0; transform: translateY(-8px); }
  to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 1024px) {
  .settings-grid {
    grid-template-columns: 1fr;
  }
}
</style>
