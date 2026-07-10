<script setup lang="ts">
import { ref } from 'vue'
import draggable from 'vuedraggable'

interface TaskActivity {
  id: string
  title: string
  type: 'video' | 'quiz' | 'pdf'
  pts: number
  duration: string
}

// Left Toolbox: Templates (Clonable)
const availableActivities = ref<TaskActivity[]>([
  { id: 't_1', title: '🎬 Onboarding Intro Video', type: 'video', pts: 10, duration: '5 min' },
  { id: 't_2', title: '🎬 Founders Fireside Video', type: 'video', pts: 15, duration: '8 min' },
  { id: 't_3', title: '❓ Security Standard Quiz', type: 'quiz', pts: 25, duration: '12 min' },
  { id: 't_4', title: '❓ GDPR Compliance Quiz', type: 'quiz', pts: 30, duration: '15 min' },
  { id: 't_5', title: '📄 Dev Environment Guide', type: 'pdf', pts: 20, duration: '20 min' },
  { id: 't_6', title: '📄 Employee Code of Conduct', type: 'pdf', pts: 15, duration: '15 min' }
])

// Right Droppable Targets: Weeks
const week1Tasks = ref<TaskActivity[]>([
  { id: 'w1_1', title: '🎬 Onboarding Intro Video', type: 'video', pts: 10, duration: '5 min' },
  { id: 'w1_2', title: '📄 Employee Code of Conduct', type: 'pdf', pts: 15, duration: '15 min' }
])

const week2Tasks = ref<TaskActivity[]>([
  { id: 'w2_1', title: '❓ Security Standard Quiz', type: 'quiz', pts: 25, duration: '12 min' }
])

let dragIdCount = 1000
function cloneActivity(original: TaskActivity) {
  return {
    ...original,
    id: `act_${original.type}_${dragIdCount++}`
  }
}

const showSavedNotification = ref(false)
const savedConfigJSON = ref('')

function saveTimeline() {
  const config = {
    week1: week1Tasks.value,
    week2: week2Tasks.value
  }
  savedConfigJSON.value = JSON.stringify(config, null, 2)
  showSavedNotification.value = true
  setTimeout(() => {
    showSavedNotification.value = false
  }, 3500)
}

function removeTask(week: 1 | 2, index: number) {
  if (week === 1) {
    week1Tasks.value.splice(index, 1)
  } else {
    week2Tasks.value.splice(index, 1)
  }
}
</script>

<template>
  <div class="journey-builder-wrapper">
    <div class="builder-header">
      <h1 class="page-title">Journey Builder</h1>
      <p class="page-subtitle">Design the onboarding roadmap. Drag task templates from the left and drop them into the weekly timeline modules.</p>
    </div>

    <!-- Main Two-Column Layout -->
    <div class="builder-columns">
      <!-- Left Column: Activity Templates Toolbox -->
      <aside class="toolbox-panel">
        <h2 class="panel-title">Activity Toolbox</h2>
        <p class="panel-subtitle">Drag these templates into the timeline</p>
        
        <draggable
          class="toolbox-drag-list"
          v-model="availableActivities"
          :group="{ name: 'journey-tasks', pull: 'clone', put: false }"
          :clone="cloneActivity"
          item-key="id"
        >
          <template #item="{ element }">
            <div class="toolbox-card" :class="element.type">
              <div class="card-left">
                <span class="activity-title">{{ element.title }}</span>
                <span class="activity-meta font-mono">{{ element.duration }} • {{ element.pts }} Pts</span>
              </div>
              <div class="drag-handle">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="dots-icon">
                  <circle cx="9" cy="5" r="1"></circle>
                  <circle cx="9" cy="12" r="1"></circle>
                  <circle cx="9" cy="19" r="1"></circle>
                  <circle cx="15" cy="5" r="1"></circle>
                  <circle cx="15" cy="12" r="1"></circle>
                  <circle cx="15" cy="19" r="1"></circle>
                </svg>
              </div>
            </div>
          </template>
        </draggable>
      </aside>

      <!-- Right Column: Module Timeline -->
      <main class="timeline-panel">
        <div class="panel-header">
          <h2 class="panel-title">Onboarding Modules Timeline</h2>
          <button @click="saveTimeline" class="save-btn" :style="{ backgroundColor: 'var(--primary-color)' }">
            Save Timeline
          </button>
        </div>

        <div class="timeline-weeks">
          <!-- Week 1 Box -->
          <div class="week-box">
            <div class="week-header">
              <span class="week-title">Week 1 Onboarding Module</span>
              <span class="week-meta">{{ week1Tasks.reduce((acc, c) => acc + c.pts, 0) }} Pts Total</span>
            </div>
            
            <draggable
              class="week-drag-area"
              v-model="week1Tasks"
              group="journey-tasks"
              item-key="id"
              ghost-class="ghost-card"
            >
              <template #item="{ element, index }">
                <div class="dropped-task-card" :class="element.type">
                  <div class="task-info">
                    <span class="task-title">{{ element.title }}</span>
                    <span class="task-sub font-mono">{{ element.duration }} • +{{ element.pts }} Points</span>
                  </div>
                  <button @click="removeTask(1, index)" class="remove-btn" title="Remove Activity">
                    &times;
                  </button>
                </div>
              </template>
              <template #header>
                <div v-if="week1Tasks.length === 0" class="empty-drop-placeholder">
                  📥 Drag and drop activities here for Week 1
                </div>
              </template>
            </draggable>
          </div>

          <!-- Week 2 Box -->
          <div class="week-box">
            <div class="week-header">
              <span class="week-title">Week 2 Onboarding Module</span>
              <span class="week-meta">{{ week2Tasks.reduce((acc, c) => acc + c.pts, 0) }} Pts Total</span>
            </div>
            
            <draggable
              class="week-drag-area"
              v-model="week2Tasks"
              group="journey-tasks"
              item-key="id"
              ghost-class="ghost-card"
            >
              <template #item="{ element, index }">
                <div class="dropped-task-card" :class="element.type">
                  <div class="task-info">
                    <span class="task-title">{{ element.title }}</span>
                    <span class="task-sub font-mono">{{ element.duration }} • +{{ element.pts }} Points</span>
                  </div>
                  <button @click="removeTask(2, index)" class="remove-btn" title="Remove Activity">
                    &times;
                  </button>
                </div>
              </template>
              <template #header>
                <div v-if="week2Tasks.length === 0" class="empty-drop-placeholder">
                  📥 Drag and drop activities here for Week 2
                </div>
              </template>
            </draggable>
          </div>
        </div>
      </main>
    </div>

    <!-- Saved Timeline Mock Panel -->
    <div v-if="showSavedNotification" class="config-modal">
      <div class="modal-card">
        <h3 class="modal-title">Timeline Saved Successfully!</h3>
        <p class="modal-desc">Mock JSON payload sent to database server:</p>
        <pre class="json-output font-mono"><code>{{ savedConfigJSON }}</code></pre>
        <button @click="showSavedNotification = false" class="close-modal-btn">Dismiss</button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.journey-builder-wrapper {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.builder-header {
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
  max-width: 800px;
  line-height: 1.6;
}

/* Two-Column Structure */
.builder-columns {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 32px;
  align-items: start;
}

/* Toolbox Column */
.toolbox-panel {
  background: rgba(15, 23, 42, 0.5);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  padding: 28px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
  text-align: left;
}

.panel-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #ffffff;
  margin: 0 0 6px 0;
}

.panel-subtitle {
  font-size: 0.85rem;
  color: #94a3b8;
  margin: 0 0 24px 0;
}

.toolbox-drag-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  min-height: 400px;
}

.toolbox-card {
  background: rgba(255, 255, 255, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 14px;
  padding: 14px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  cursor: grab;
  transition: all 0.2s ease;
}

.toolbox-card:hover {
  background: rgba(255, 255, 255, 0.05);
  border-color: rgba(255, 255, 255, 0.12);
  transform: translateY(-1px);
}

.toolbox-card:active {
  cursor: grabbing;
}

.card-left {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.activity-title {
  font-size: 0.9rem;
  font-weight: 600;
  color: #ffffff;
}

.activity-meta {
  font-size: 0.75rem;
  color: #cbd5e1;
}

.drag-handle {
  color: #475569;
  display: flex;
  align-items: center;
}

.dots-icon {
  width: 16px;
  height: 16px;
}

/* Timeline Column */
.timeline-panel {
  background: rgba(15, 23, 42, 0.5);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  padding: 32px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
  text-align: left;
}

.timeline-panel .panel-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 28px;
}

.save-btn {
  border: none;
  color: white;
  border-radius: 12px;
  padding: 10px 20px;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 4px 14px rgba(79, 70, 229, 0.25);
  transition: all 0.2s ease;
}

.save-btn:hover {
  filter: brightness(1.1);
  transform: translateY(-1px);
}

.timeline-weeks {
  display: flex;
  flex-direction: column;
  gap: 28px;
}

.week-box {
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.01);
  overflow: hidden;
}

.week-header {
  background: rgba(255, 255, 255, 0.02);
  padding: 14px 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.week-title {
  font-size: 0.95rem;
  font-weight: 700;
  color: #ffffff;
}

.week-meta {
  font-size: 0.8rem;
  color: #fbbf24;
  font-weight: 600;
}

.week-drag-area {
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  min-height: 120px;
}

.dropped-task-card {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-left: 4px solid var(--primary-color);
  border-radius: 12px;
  padding: 12px 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  cursor: grab;
}

.dropped-task-card:hover {
  background: rgba(255, 255, 255, 0.05);
}

.task-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.task-title {
  font-size: 0.9rem;
  font-weight: 600;
  color: #ffffff;
}

.task-sub {
  font-size: 0.75rem;
  color: #94a3b8;
}

.remove-btn {
  background: none;
  border: none;
  color: #94a3b8;
  font-size: 1.3rem;
  cursor: pointer;
  padding: 0 4px;
  line-height: 1;
  transition: color 0.2s ease;
}

.remove-btn:hover {
  color: #ef4444;
}

.empty-drop-placeholder {
  border: 2px dashed rgba(255, 255, 255, 0.08);
  border-radius: 12px;
  padding: 24px;
  text-align: center;
  font-size: 0.85rem;
  color: #64748b;
}

.ghost-card {
  opacity: 0.4;
  background: rgba(79, 70, 229, 0.1);
  border-style: dashed;
}

/* Config Output Overlay */
.config-modal {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 200;
}

.modal-card {
  background: #0f172a;
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  padding: 32px;
  max-width: 500px;
  width: 90%;
  box-shadow: 0 20px 40px rgba(0,0,0,0.5);
  text-align: left;
  animation: scaleIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.modal-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: #10b981;
  margin: 0 0 8px 0;
}

.modal-desc {
  font-size: 0.9rem;
  color: #94a3b8;
  margin: 0 0 16px 0;
}

.json-output {
  background: #020617;
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 12px;
  padding: 16px;
  height: 240px;
  overflow: auto;
  font-size: 0.8rem;
  color: #38bdf8;
  margin-bottom: 24px;
}

.close-modal-btn {
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.12);
  color: white;
  border-radius: 10px;
  padding: 10px 20px;
  font-weight: 600;
  font-size: 0.85rem;
  cursor: pointer;
  width: 100%;
}

.close-modal-btn:hover {
  background: rgba(255, 255, 255, 0.12);
}

@keyframes scaleIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}

@media (max-width: 1024px) {
  .builder-columns {
    grid-template-columns: 1fr;
  }
}
</style>
