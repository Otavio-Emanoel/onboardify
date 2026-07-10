<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import ModuleCard from '../../components/ModuleCard.vue'

const router = useRouter()

interface Task {
  id: string
  title: string
  duration: string
  type: 'video' | 'quiz' | 'pdf'
  pts: number
  completed: boolean
}

interface Module {
  id: string
  title: string
  description: string
  unlocked: boolean
  progress: number
  week: string
  tasks: Task[]
}

// Mock Journey Modules
const modules = ref<Module[]>([
  {
    id: 'mod_1',
    title: 'Welcome & System Setup',
    description: 'Configure your hardware, complete basic security protocols, and set up your initial communications workspace.',
    unlocked: true,
    progress: 100,
    week: 'Week 1',
    tasks: [
      { id: '1', title: 'Watch Welcome Video', duration: '5 min', type: 'video', pts: 10, completed: true },
      { id: '2', title: 'Environment Setup Checklist', duration: '15 min', type: 'pdf', pts: 20, completed: true }
    ]
  },
  {
    id: 'mod_2',
    title: 'Company Culture & Values',
    description: 'Learn about our core mission, operational frameworks, and hear from team leaders about how we collaborate.',
    unlocked: true,
    progress: 20,
    week: 'Week 1',
    tasks: [
      { id: '3', title: 'Onboarding Quiz: Security Policies', duration: '10 min', type: 'quiz', pts: 30, completed: false },
      { id: '4', title: 'Founder Mission Fireside Video', duration: '8 min', type: 'video', pts: 15, completed: false },
      { id: '5', title: 'Employee Handbook Guidebook', duration: '20 min', type: 'pdf', pts: 25, completed: false }
    ]
  },
  {
    id: 'mod_3',
    title: 'Security Compliance & IT',
    description: 'Verify configuration of multi-factor keys, review client data safety guidelines, and complete basic training compliance tests.',
    unlocked: false,
    progress: 0,
    week: 'Week 2',
    tasks: [
      { id: '6', title: 'GDPR Compliance Quiz', duration: '12 min', type: 'quiz', pts: 35, completed: false },
      { id: '7', title: 'Secure Coding & IT Checklist', duration: '10 min', type: 'pdf', pts: 20, completed: false }
    ]
  },
  {
    id: 'mod_4',
    title: 'Role Deep Dive & Tooling',
    description: 'Gain sandbox environment keys, clone central repositories, run locally and review the architectural design charts.',
    unlocked: false,
    progress: 0,
    week: 'Week 2',
    tasks: [
      { id: '8', title: 'Sandbox Environment Onboarding Video', duration: '15 min', type: 'video', pts: 25, completed: false },
      { id: '9', title: 'First Local Build Checklist', duration: '30 min', type: 'pdf', pts: 50, completed: false }
    ]
  }
])

const selectedModule = ref<Module | null>(modules.value[1]) // Default select module 2

function selectModule(mod: Module) {
  if (mod.unlocked) {
    selectedModule.value = mod
  }
}

function handleGoToTask(taskId: string) {
  router.push(`/task/${taskId}`)
}

function getTaskIcon(type: string) {
  if (type === 'video') return '🎬'
  if (type === 'quiz') return '❓'
  return '📄'
}
</script>

<template>
  <div class="journey-wrapper">
    <div class="journey-header">
      <h1 class="page-title">Onboarding Journey Map</h1>
      <p class="page-subtitle">Your visual roadmap to onboarding success. Complete modules to unlock subsequent weeks.</p>
    </div>

    <div class="journey-content-grid">
      <!-- Left side: Module Cards Grid -->
      <div class="modules-column">
        <h2 class="section-title">My Modules Roadmap</h2>
        <div class="modules-grid">
          <ModuleCard 
            v-for="mod in modules" 
            :key="mod.id"
            :title="mod.title"
            :description="mod.description"
            :unlocked="mod.unlocked"
            :progress="mod.progress"
            :taskCount="mod.tasks.length"
            :week="mod.week"
            :class="{ 'is-selected': selectedModule?.id === mod.id }"
            @click="selectModule(mod)"
          />
        </div>
      </div>

      <!-- Right side: Selected Module Task List Drawer -->
      <div class="tasks-column">
        <div v-if="selectedModule" class="tasks-panel">
          <div class="panel-header">
            <span class="panel-week-tag">{{ selectedModule.week }}</span>
            <h3 class="panel-title">{{ selectedModule.title }}</h3>
            <p class="panel-desc">{{ selectedModule.description }}</p>
          </div>

          <div class="panel-progress-box">
            <div class="progress-details">
              <span>Completed Tasks</span>
              <span class="progress-ratio font-mono">
                {{ selectedModule.tasks.filter(t => t.completed).length }}/{{ selectedModule.tasks.length }}
              </span>
            </div>
            <div class="progress-bar-container">
              <div class="progress-bar" :style="{ width: `${selectedModule.progress}%`, backgroundColor: 'var(--primary-color)' }"></div>
            </div>
          </div>

          <div class="panel-body">
            <h4 class="task-list-title">Tasks Inside This Module</h4>
            <div class="task-items-list">
              <div 
                v-for="task in selectedModule.tasks" 
                :key="task.id" 
                @click="handleGoToTask(task.id)"
                class="task-item-card"
                :class="{ 'task-completed': task.completed }"
              >
                <div class="task-item-left">
                  <div class="task-status-indicator">
                    <svg v-if="task.completed" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="tick-icon">
                      <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span v-else class="bullet-dot"></span>
                  </div>
                  <div class="task-meta">
                    <span class="task-item-title">{{ task.title }}</span>
                    <span class="task-item-sub">{{ getTaskIcon(task.type) }} {{ task.type.toUpperCase() }} • {{ task.duration }}</span>
                  </div>
                </div>
                <div class="task-item-right">
                  <span class="points-label">+{{ task.pts }} Pts</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="tasks-panel empty-panel">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="map-icon">
            <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21 3 6"></polygon>
            <line x1="9" y1="3" x2="9" y2="18"></line>
            <line x1="15" y1="6" x2="15" y2="21"></line>
          </svg>
          <p>Select an unlocked module from the roadmap to view and start tasks.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.journey-wrapper {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.journey-header {
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

.journey-content-grid {
  display: grid;
  grid-template-columns: 1.6fr 1.2fr;
  gap: 32px;
  align-items: start;
}

.modules-column, .tasks-column {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.section-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: #ffffff;
  margin: 0;
  text-align: left;
}

.modules-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
}

.modules-grid :deep(.module-card.is-selected) {
  border-color: var(--primary-color);
  background: rgba(255, 255, 255, 0.05);
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2), 0 8px 24px rgba(0, 0, 0, 0.2);
}

/* Tasks Side Panel */
.tasks-panel {
  background: rgba(15, 23, 42, 0.85);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  padding: 32px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
  text-align: left;
  position: sticky;
  top: 96px; /* Below sticky navbar */
}

.panel-header {
  margin-bottom: 24px;
}

.panel-week-tag {
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 1px;
  color: var(--primary-color);
  text-transform: uppercase;
  background: rgba(79, 70, 229, 0.12);
  padding: 4px 10px;
  border-radius: 9999px;
}

.panel-title {
  font-size: 1.4rem;
  font-weight: 700;
  color: #ffffff;
  margin: 12px 0 8px 0;
}

.panel-desc {
  font-size: 0.85rem;
  color: #cbd5e1;
  line-height: 1.5;
  margin: 0;
}

.panel-progress-box {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 16px;
  padding: 16px;
  margin-bottom: 28px;
}

.progress-details {
  display: flex;
  justify-content: space-between;
  font-size: 0.8rem;
  font-weight: 600;
  color: #94a3b8;
  margin-bottom: 8px;
}

.progress-ratio {
  color: #ffffff;
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
}

.task-list-title {
  font-size: 0.9rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: #94a3b8;
  margin: 0 0 16px 0;
}

.task-items-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.task-item-card {
  background: rgba(255, 255, 255, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 12px;
  padding: 14px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  cursor: pointer;
  transition: all 0.2s ease;
}

.task-item-card:hover {
  background: rgba(255, 255, 255, 0.05);
  border-color: rgba(255, 255, 255, 0.12);
  transform: translateX(2px);
}

.task-item-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.task-status-indicator {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 2px solid rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all 0.2s ease;
}

.bullet-dot {
  width: 6px;
  height: 6px;
  background-color: transparent;
  border-radius: 50%;
}

.task-completed .task-status-indicator {
  border-color: #10b981;
  background: rgba(16, 185, 129, 0.15);
}

.tick-icon {
  width: 10px;
  height: 10px;
  color: #10b981;
}

.task-meta {
  display: flex;
  flex-direction: column;
}

.task-item-title {
  font-size: 0.9rem;
  font-weight: 600;
  color: #f1f5f9;
  line-height: 1.3;
}

.task-item-sub {
  font-size: 0.75rem;
  color: #94a3b8;
  margin-top: 2px;
}

.points-label {
  font-size: 0.8rem;
  font-weight: 700;
  color: #fbbf24;
  background: rgba(251, 191, 36, 0.12);
  padding: 2px 8px;
  border-radius: 6px;
}

.empty-panel {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 48px;
  color: #94a3b8;
}

.map-icon {
  width: 48px;
  height: 48px;
  margin-bottom: 16px;
  opacity: 0.6;
}

@media (max-width: 1024px) {
  .journey-content-grid {
    grid-template-columns: 1fr;
  }
}
</style>
