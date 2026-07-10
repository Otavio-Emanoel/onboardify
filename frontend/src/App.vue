<script setup lang="ts">
import { onMounted } from 'vue'
import { useTenantStore } from './stores/useTenantStore'

const tenantStore = useTenantStore()

onMounted(() => {
  tenantStore.fetchBranding()
})
</script>

<template>
  <router-view />
</template>

<style>
/* Global resets or overrides if needed */
</style>

