<script setup lang="ts">

const props = withDefaults(
  defineProps<{
    title: string
    description: string
    unlocked: boolean
    progress: number
    taskCount: number
    week: string
  }>(),
  {
    unlocked: false,
    progress: 0,
    taskCount: 0,
    week: 'Week 1'
  }
)

const emit = defineEmits<{
  (e: 'click'): void
}>()

function handleClick() {
  if (props.unlocked) {
    emit('click')
  }
}
</script>

<template>
  <div 
    class="module-card" 
    :class="{ 'is-locked': !unlocked, 'is-completed': progress === 100 }"
    @click="handleClick"
  >
    <div class="card-header">
      <span class="week-tag">{{ week }}</span>
      <div class="status-icon">
        <svg v-if="!unlocked" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="lock-svg">
          <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
          <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
        </svg>
        <svg v-else-if="progress === 100" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="check-svg">
          <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <span v-else class="progress-pct">{{ progress }}%</span>
      </div>
    </div>

    <div class="card-body">
      <h3 class="module-title">{{ title }}</h3>
      <p class="module-desc">{{ description }}</p>
    </div>

    <div class="card-footer">
      <div class="task-info">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="book-icon">
          <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
          <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
        </svg>
        <span>{{ taskCount }} Tasks</span>
      </div>

      <!-- Module Progress Bar -->
      <div v-if="unlocked" class="progress-bar-container">
        <div class="progress-bar" :style="{ width: `${progress}%`, backgroundColor: 'var(--primary-color)' }"></div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.module-card {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 20px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  gap: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15), 
              inset 0 1px 0 rgba(255, 255, 255, 0.05);
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

.module-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background: transparent;
  transition: background 0.3s ease;
}

.module-card:hover:not(.is-locked) {
  transform: translateY(-4px);
  background: rgba(255, 255, 255, 0.05);
  border-color: rgba(255, 255, 255, 0.15);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3), 
              inset 0 1px 0 rgba(255, 255, 255, 0.08);
}

.module-card:hover:not(.is-locked)::before {
  background: var(--primary-color);
}

/* Locked Styling */
.is-locked {
  opacity: 0.45;
  cursor: not-allowed;
  background: rgba(255, 255, 255, 0.01);
  filter: grayscale(100%);
}

.is-completed {
  border-color: rgba(16, 185, 129, 0.3);
}

.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.week-tag {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: #94a3b8;
  background: rgba(255, 255, 255, 0.06);
  padding: 4px 10px;
  border-radius: 9999px;
}

.status-icon {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.08);
  font-size: 0.75rem;
  font-weight: 600;
}

.lock-svg {
  width: 14px;
  height: 14px;
  color: #94a3b8;
}

.check-svg {
  width: 14px;
  height: 14px;
  color: #10b981;
}

.progress-pct {
  color: #38bdf8;
}

.card-body {
  text-align: left;
}

.module-title {
  font-size: 1.15rem;
  font-weight: 600;
  margin: 0 0 8px 0;
  color: #ffffff;
}

.module-desc {
  font-size: 0.85rem;
  color: #94a3b8;
  line-height: 1.5;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.card-footer {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.task-info {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.8rem;
  color: #cbd5e1;
  text-align: left;
}

.book-icon {
  width: 14px;
  height: 14px;
  color: #cbd5e1;
}

.progress-bar-container {
  height: 6px;
  background: rgba(255, 255, 255, 0.08);
  border-radius: 9999px;
  overflow: hidden;
}

.progress-bar {
  height: 100%;
  border-radius: 9999px;
  transition: width 0.4s ease;
}
</style>
