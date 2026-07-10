<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import api from '../../services/api'

interface Employee {
  id: string
  name: string
  email: string
  department: string
  hireDate: string
  progress: number
  level: number
  points: number
  status: 'on_track' | 'completed' | 'behind'
}

const employees = ref<Employee[]>([])
const isLoading = ref(true)

onMounted(async () => {
  try {
    const response = await api.get('/admin/employees')
    employees.value = response.data
  } catch (e) {
    console.error('Failed to load active hires metrics', e)
  } finally {
    isLoading.value = false
  }
})

// Stats widgets calculations
const totalHires = computed(() => employees.value.length)
const avgProgress = computed(() => {
  if (employees.value.length === 0) return 0
  return Math.round(employees.value.reduce((acc, curr) => acc + curr.progress, 0) / employees.value.length)
})
const completedHires = computed(() => employees.value.filter(e => e.progress === 100).length)
const behindHires = computed(() => employees.value.filter(e => e.status === 'behind').length)

function getInitials(name: string) {
  const parts = name.split(' ')
  if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase()
  return name.slice(0, 2).toUpperCase()
}
</script>

<template>
  <div class="admin-dashboard-wrapper">
    <div class="dashboard-header">
      <h1 class="page-title">Admin Dashboard</h1>
      <p class="page-subtitle">Monitor employee onboarding completion rates and department metrics.</p>
    </div>

    <!-- Stats Widgets Container -->
    <div class="stats-row">
      <div class="stat-card">
        <span class="stat-title">Active New Hires</span>
        <div class="stat-value">{{ totalHires }}</div>
        <span class="stat-trend up">📈 Steady Growth</span>
      </div>

      <div class="stat-card">
        <span class="stat-title">Average Progress</span>
        <div class="stat-value font-mono">{{ avgProgress }}%</div>
        <div class="progress-bar-mini">
          <div class="bar" :style="{ width: `${avgProgress}%`, backgroundColor: 'var(--primary-color)' }"></div>
        </div>
      </div>

      <div class="stat-card">
        <span class="stat-title">Completed Journey</span>
        <div class="stat-value">{{ completedHires }}</div>
        <span class="stat-trend success">🎉 Ready for Duty</span>
      </div>

      <div class="stat-card">
        <span class="stat-title">Needs Attention</span>
        <div class="stat-value warning">{{ behindHires }}</div>
        <span class="stat-trend danger">⚠️ Falling Behind</span>
      </div>
    </div>

    <!-- Active Employees Table -->
    <div class="table-card">
      <div class="table-header">
        <h2 class="table-title">Employee Onboarding Tracker</h2>
        <div class="table-actions">
          <button class="export-btn">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="btn-icon">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>Export CSV</span>
          </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="employees-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Department</th>
              <th>Hire Date</th>
              <th>Progress</th>
              <th>Stats</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="emp in employees" :key="emp.id" class="table-row">
              <td>
                <div class="employee-profile">
                  <div class="profile-avatar" :style="{ backgroundColor: 'var(--primary-color)' }">
                    {{ getInitials(emp.name) }}
                  </div>
                  <div class="profile-details">
                    <span class="emp-name">{{ emp.name }}</span>
                    <span class="emp-email">{{ emp.email }}</span>
                  </div>
                </div>
              </td>
              <td>{{ emp.department }}</td>
              <td class="font-mono text-sm">{{ emp.hireDate }}</td>
              <td>
                <div class="progress-cell">
                  <div class="bar-container">
                    <div class="bar" :style="{ width: `${emp.progress}%`, backgroundColor: emp.progress === 100 ? '#10b981' : 'var(--primary-color)' }"></div>
                  </div>
                  <span class="progress-percent font-mono">{{ emp.progress }}%</span>
                </div>
              </td>
              <td>
                <div class="emp-stats">
                  <span class="stat-badge level-badge">Lvl {{ emp.level }}</span>
                  <span class="stat-badge points-badge">{{ emp.points }} Pts</span>
                </div>
              </td>
              <td>
                <span class="status-pill" :class="emp.status">
                  {{ emp.status === 'completed' ? 'Completed' : emp.status === 'on_track' ? 'On Track' : 'Behind' }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped>
.admin-dashboard-wrapper {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.dashboard-header {
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

/* Stats Cards */
.stats-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 24px;
}

.stat-card {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 20px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  text-align: left;
}

.stat-title {
  font-size: 0.85rem;
  font-weight: 600;
  color: #94a3b8;
}

.stat-value {
  font-size: 2.2rem;
  font-weight: 800;
  color: #ffffff;
  margin: 12px 0 8px 0;
}

.stat-value.warning {
  color: #f59e0b;
}

.stat-trend {
  font-size: 0.8rem;
  font-weight: 600;
}

.stat-trend.up {
  color: #38bdf8;
}

.stat-trend.success {
  color: #34d399;
}

.stat-trend.danger {
  color: #f87171;
}

.progress-bar-mini {
  width: 100%;
  height: 4px;
  background: rgba(255, 255, 255, 0.08);
  border-radius: 9999px;
  overflow: hidden;
  margin-top: 12px;
}

.progress-bar-mini .bar {
  height: 100%;
}

/* Table Card */
.table-card {
  background: rgba(15, 23, 42, 0.5);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  padding: 32px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  text-align: left;
}

.table-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
}

.table-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: #ffffff;
  margin: 0;
}

.export-btn {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 12px;
  padding: 10px 18px;
  font-size: 0.85rem;
  font-weight: 600;
  color: #ffffff;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s ease;
}

.export-btn:hover {
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(255, 255, 255, 0.15);
}

.btn-icon {
  width: 14px;
  height: 14px;
}

.table-responsive {
  overflow-x: auto;
}

.employees-table {
  width: 100%;
  border-collapse: collapse;
}

.employees-table th {
  padding: 16px 20px;
  font-size: 0.8rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: #94a3b8;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.employees-table td {
  padding: 18px 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  color: #cbd5e1;
  font-size: 0.95rem;
}

.table-row:hover {
  background: rgba(255, 255, 255, 0.02);
}

.employee-profile {
  display: flex;
  align-items: center;
  gap: 14px;
}

.profile-avatar {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
  font-weight: 700;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.profile-details {
  display: flex;
  flex-direction: column;
}

.emp-name {
  font-weight: 600;
  color: #ffffff;
}

.emp-email {
  font-size: 0.8rem;
  color: #94a3b8;
  margin-top: 2px;
}

.progress-cell {
  display: flex;
  align-items: center;
  gap: 12px;
}

.bar-container {
  width: 120px;
  height: 6px;
  background: rgba(255, 255, 255, 0.08);
  border-radius: 9999px;
  overflow: hidden;
}

.bar-container .bar {
  height: 100%;
  border-radius: 9999px;
}

.progress-percent {
  font-size: 0.85rem;
  font-weight: 600;
  color: #ffffff;
}

.emp-stats {
  display: flex;
  align-items: center;
  gap: 8px;
}

.stat-badge {
  font-size: 0.75rem;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 6px;
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.level-badge {
  background: rgba(56, 189, 248, 0.1);
  color: #38bdf8;
  border-color: rgba(56, 189, 248, 0.2);
}

.points-badge {
  background: rgba(251, 191, 36, 0.1);
  color: #fbbf24;
  border-color: rgba(251, 191, 36, 0.2);
}

.status-pill {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  padding: 4px 10px;
  border-radius: 9999px;
}

.status-pill.completed {
  background: rgba(16, 185, 129, 0.12);
  color: #34d399;
  border: 1px solid rgba(16, 185, 129, 0.2);
}

.status-pill.on_track {
  background: rgba(59, 130, 246, 0.12);
  color: #60a5fa;
  border: 1px solid rgba(59, 130, 246, 0.2);
}

.status-pill.behind {
  background: rgba(239, 68, 68, 0.12);
  color: #f87171;
  border: 1px solid rgba(239, 68, 68, 0.2);
}
</style>
