<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/useAuthStore'
import api from '../../services/api'

const authStore = useAuthStore()
const router = useRouter()

const userName = computed(() => authStore.user?.name || 'New Hire')

interface Task {
  id: string
  title: string
  duration: string
  type: 'video' | 'quiz' | 'pdf'
  pts: number
  completed: boolean
  description: string
}

interface Module {
  id: string
  title: string
  description: string
  week: string
  progress: number
  tasks: Task[]
}

const modules = ref<Module[]>([])
const isLoading = ref(true)

// Fetch employee modules on mount
onMounted(async () => {
  try {
    const response = await api.get('/employee/modules')
    modules.value = response.data
  } catch (e) {
    console.error('Failed to load employee dashboard details', e)
  } finally {
    isLoading.value = false
  }
})

// Calculate overall progress across all tasks
const progress = computed(() => {
  let totalTasks = 0
  let completedTasks = 0
  modules.value.forEach((mod) => {
    mod.tasks.forEach((t) => {
      totalTasks++
      if (t.completed) {
        completedTasks++
      }
    })
  })
  return totalTasks > 0 ? Math.round((completedTasks / totalTasks) * 100) : 0
})

const weekLabel = computed(() => {
  const nextIncomplete = nextTask.value
  if (nextIncomplete) {
    const parentMod = modules.value.find(mod => mod.tasks.some(t => t.id === nextIncomplete.id))
    if (parentMod) return parentMod.week
  }
  return 'Week 1'
})

// Circular progress calculations
const radius = 80
const circumference = 2 * Math.PI * radius
const strokeDashoffset = computed(() => {
  return circumference - (progress.value / 100) * circumference
})

// Find next task to resume onboarding
const nextTask = computed(() => {
  for (const mod of modules.value) {
    for (const task of mod.tasks) {
      if (!task.completed) {
        return task
      }
    }
  }
  return null
})

function resumeJourney() {
  if (nextTask.value) {
    router.push(`/task/${nextTask.value.id}`)
  } else {
    router.push('/journey')
  }
}

// Show up to 3 upcoming incomplete tasks in dashboard preview
const upcomingTasks = computed(() => {
  const tasksList: Task[] = []
  modules.value.forEach((mod) => {
    mod.tasks.forEach((t) => {
      if (!t.completed && tasksList.length < 3) {
        tasksList.push(t)
      }
    })
  })
  return tasksList
})

function getTaskIcon(type: string) {
  if (type === 'video') return '🎬'
  if (type === 'quiz') return '❓'
  return '📄'
}

function handleGoToTask(taskId: string) {
  router.push(`/task/${taskId}`)
}
</script>

<template>
  <div class="dashboard-wrapper">
    <!-- Welcome Header -->
    <section class="welcome-section">
      <div class="welcome-text">
        <h1 class="welcome-title">Welcome back, <span class="gradient-name">{{ userName }}</span>!</h1>
        <p class="welcome-desc">Your onboarding hub is ready. Let's make today productive and build your journey map.</p>
      </div>
    </section>

    <!-- Progress & CTA Grid -->
    <div class="dashboard-grid">
      <!-- Circular Progress Ring Card -->
      <div class="progress-card">
        <h2 class="card-title">Overall Progress</h2>
        
        <div class="circle-container">
          <svg class="progress-ring" width="200" height="200">
            <!-- Background Ring -->
            <circle
              class="progress-ring-bg"
              stroke="rgba(255,255,255,0.05)"
              stroke-width="12"
              fill="transparent"
              r="80"
              cx="100"
              cy="100"
            />
            <!-- Foreground Ring -->
            <circle
              class="progress-ring-fg"
              stroke="var(--primary-color)"
              stroke-width="12"
              stroke-linecap="round"
              fill="transparent"
              r="80"
              cx="100"
              cy="100"
              :stroke-dasharray="`${circumference} ${circumference}`"
              :stroke-dashoffset="strokeDashoffset"
            />
          </svg>
          
          <div class="circle-text">
            <span class="percentage font-mono">{{ progress }}%</span>
            <span class="label">Completed</span>
          </div>
        </div>

        <p class="progress-sub">{{ weekLabel }} Onboarding Goals</p>
      </div>

      <!-- Resume Journey CTA Card -->
      <div class="cta-card">
        <div class="cta-content">
          <div class="cta-badge">IN PROGRESS</div>
          <h2 class="cta-title">Next up: {{ nextTask?.title || 'All Tasks Completed!' }}</h2>
          <p class="cta-desc">{{ nextTask?.description || 'You have successfully finished all assigned onboarding modules. Great job!' }}</p>
          
          <button @click="resumeJourney" class="resume-btn">
            <span>Resume Journey</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="arrow-icon">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Upcoming Tasks Section -->
    <section class="tasks-section">
      <div class="section-header">
        <h2 class="section-title">Today's Tasks Preview</h2>
        <router-link to="/journey" class="view-all-link">View Full Journey Map</router-link>
      </div>

      <div class="tasks-list">
        <div 
          v-for="task in upcomingTasks" 
          :key="task.id" 
          @click="handleGoToTask(task.id)"
          class="task-row"
        >
          <div class="task-left">
            <span class="task-type-icon">{{ getTaskIcon(task.type) }}</span>
            <div class="task-details">
              <span class="task-title">{{ task.title }}</span>
              <span class="task-duration">{{ task.duration }} • {{ task.pts }} Points</span>
            </div>
          </div>
          <button class="start-task-btn">
            <span>Start</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="play-icon">
              <polygon points="5 3 19 12 5 21 5 3"></polygon>
            </svg>
          </button>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
.dashboard-wrapper {
  display: flex;
  flex-direction: column;
  gap: 40px;
}

.welcome-section {
  text-align: left;
}

.welcome-title {
  font-size: 2.2rem;
  font-weight: 700;
  margin: 0 0 8px 0;
  letter-spacing: -0.8px;
}

.gradient-name {
  background: linear-gradient(135deg, var(--primary-color) 0%, #38bdf8 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.welcome-desc {
  font-size: 1.05rem;
  color: #94a3b8;
  max-width: 680px;
  line-height: 1.6;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: 1fr 1.8fr;
  gap: 32px;
}

/* Progress Ring Card */
.progress-card {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  padding: 32px;
  display: flex;
  flex-direction: column;
  align-items: center;
  box-shadow: 0 8px 30px rgba(0,0,0,0.2);
}

.card-title {
  font-size: 1.15rem;
  font-weight: 600;
  color: #ffffff;
  margin: 0 0 24px 0;
  align-self: flex-start;
}

.circle-container {
  position: relative;
  width: 200px;
  height: 200px;
  margin-bottom: 16px;
}

.progress-ring {
  transform: rotate(-90deg);
}

.progress-ring-fg {
  filter: drop-shadow(0 0 8px var(--primary-color));
  transition: stroke-dashoffset 0.6s ease;
}

.circle-text {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  display: flex;
  flex-direction: column;
  align-items: center;
}

.percentage {
  font-size: 2.2rem;
  font-weight: 800;
  color: #ffffff;
  line-height: 1;
}

.label {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: #94a3b8;
  margin-top: 4px;
}

.progress-sub {
  font-size: 0.85rem;
  color: #cbd5e1;
  font-weight: 500;
  margin: 8px 0 0 0;
}

/* CTA Card */
.cta-card {
  background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.8) 100%);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  padding: 40px;
  display: flex;
  align-items: center;
  text-align: left;
  box-shadow: 0 8px 30px rgba(0,0,0,0.2);
  position: relative;
  overflow: hidden;
}

.cta-card::after {
  content: '';
  position: absolute;
  top: -50%;
  right: -20%;
  width: 300px;
  height: 300px;
  background: radial-gradient(circle, rgba(79, 70, 229, 0.15) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}

.cta-content {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 16px;
}

.cta-badge {
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 1px;
  color: #38bdf8;
  background: rgba(56, 189, 248, 0.12);
  padding: 4px 10px;
  border-radius: 9999px;
  border: 1px solid rgba(56, 189, 248, 0.2);
}

.cta-title {
  font-size: 1.6rem;
  font-weight: 700;
  color: #ffffff;
  line-height: 1.3;
  margin: 0;
}

.cta-desc {
  font-size: 0.95rem;
  color: #94a3b8;
  line-height: 1.6;
  margin: 0 0 8px 0;
  max-width: 480px;
}

.resume-btn {
  background: var(--primary-color);
  border: none;
  border-radius: 16px;
  padding: 16px 28px;
  color: #ffffff;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 12px;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  box-shadow: 0 8px 24px rgba(79, 70, 229, 0.3);
}

.resume-btn:hover {
  transform: translateY(-2px);
  filter: brightness(1.1);
  box-shadow: 0 12px 30px rgba(79, 70, 229, 0.4);
}

.arrow-icon {
  width: 18px;
  height: 18px;
  transition: transform 0.2s ease;
}

.resume-btn:hover .arrow-icon {
  transform: translateX(4px);
}

/* Tasks Preview Section */
.tasks-section {
  text-align: left;
}

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}

.section-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: #ffffff;
  margin: 0;
}

.view-all-link {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--primary-color);
  text-decoration: none;
  transition: color 0.2s ease;
}

.view-all-link:hover {
  filter: brightness(1.2);
  text-decoration: underline;
}

.tasks-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.task-row {
  background: rgba(255, 255, 255, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 16px;
  padding: 18px 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.task-row:hover {
  background: rgba(255, 255, 255, 0.04);
  border-color: rgba(255, 255, 255, 0.12);
  transform: scale(1.005);
}

.task-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.task-type-icon {
  font-size: 1.5rem;
}

.task-details {
  display: flex;
  flex-direction: column;
}

.task-title {
  font-size: 1rem;
  font-weight: 600;
  color: #f1f5f9;
}

.task-duration {
  font-size: 0.8rem;
  color: #94a3b8;
  margin-top: 2px;
}

.start-task-btn {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 10px;
  padding: 8px 16px;
  font-size: 0.85rem;
  font-weight: 600;
  color: #ffffff;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s ease;
}

.task-row:hover .start-task-btn {
  background: var(--primary-color);
  border-color: transparent;
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
}

.play-icon {
  width: 12px;
  height: 12px;
  fill: currentColor;
}

@media (max-width: 1024px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
}
</style>
