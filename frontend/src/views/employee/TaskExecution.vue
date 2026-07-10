<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/useAuthStore'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const taskId = ref('')
const isCompleted = ref(false)
const claimSuccess = ref(false)

// Video task simulation state
const isPlaying = ref(false)
const videoProgress = ref(0)

// Quiz task state
const selectedAnswer = ref<number | null>(null)
const quizChecked = ref(false)
const isQuizCorrect = ref(false)

// PDF task state
const pdfPage = ref(1)
const pdfZoom = ref(100)

interface MockTask {
  id: string
  title: string
  description: string
  type: 'video' | 'quiz' | 'pdf'
  pts: number
  // Video specific properties
  videoUrl?: string
  duration?: string
  // Quiz specific properties
  quizQuestion?: string
  quizOptions?: string[]
  quizCorrectIndex?: number
  // PDF specific properties
  pdfTitle?: string
  pdfContentPages?: string[]
}

const mockTasksData: Record<string, MockTask> = {
  '1': {
    id: '1',
    title: 'Watch Welcome Video',
    description: 'A message from our CEO welcoming you to the team and explaining our central vision and objectives.',
    type: 'video',
    pts: 10,
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-working-late-at-the-office-39794-large.mp4',
    duration: '2:15'
  },
  '2': {
    id: '2',
    title: 'Work Environment Setup Checklist',
    description: 'Follow this technical guide to configure your system preferences, access keys, and repositories.',
    type: 'pdf',
    pts: 20,
    pdfTitle: 'developer_setup_guide_v2.pdf',
    pdfContentPages: [
      'Welcome to the Development Team! \n\n1. Request GitHub Access in the Slack #it-helpdesk channel.\n2. Configure your SSH keys locally and add them to your GitHub profile.\n3. Make sure to download Node.js v20+ and NPM v10+.',
      '4. Clone the repository: git clone git@github.com:onboardify/frontend.git\n5. Run npm install to resolve project dependencies.\n6. Start the local server: npm run dev\n7. Verify access to the sandbox DB by loading /api/health.',
      '8. Configure 2FA keys for all administrative logins.\n9. Complete your environment questionnaire in the HR portal.\n10. Notify your onboarding buddy once local compilation succeeds!'
    ]
  },
  '3': {
    id: '3',
    title: 'Onboarding Quiz: Security Policies',
    description: 'Test your understanding of security standards, lock policies, and social engineering warnings.',
    type: 'quiz',
    pts: 30,
    quizQuestion: 'Which of the following is our standard policy regarding laptops in public spaces?',
    quizOptions: [
      'Laptops can be left logged-in if you are only gone for under 2 minutes.',
      'Laptops must be screen-locked and securely tethered or stored when unattended, even briefly.',
      'You are allowed to share passwords with colleagues over Slack without secure encryption.',
      'Leaving your computer unlocked in the workspace is perfectly fine.'
    ],
    quizCorrectIndex: 1
  }
}

const task = ref<MockTask>({
  id: 'default',
  title: 'Onboarding Checklist Task',
  description: 'Complete the listed criteria to obtain points.',
  type: 'pdf',
  pts: 10,
  pdfTitle: 'checklist.pdf',
  pdfContentPages: ['Default task checklist. Please complete all standard onboarding steps.']
})

onMounted(() => {
  const idStr = route.params.id as string
  taskId.value = idStr
  
  if (mockTasksData[idStr]) {
    task.value = mockTasksData[idStr]
  } else {
    // If dynamic URL param task, default to a checklist quiz
    task.value = {
      id: idStr,
      title: `Task #${idStr} Onboarding Checkpoint`,
      description: 'Dynamic onboarding task checklist.',
      type: 'quiz',
      pts: 25,
      quizQuestion: 'I confirm that I have reviewed the training materials for this checkpoint.',
      quizOptions: [
        'No, I need more time.',
        'Yes, I have completed the review and am ready to proceed.'
      ],
      quizCorrectIndex: 1
    }
  }
})

// Video simulation
let videoInterval: any = null
function togglePlay() {
  isPlaying.value = !isPlaying.value
  if (isPlaying.value) {
    videoInterval = setInterval(() => {
      if (videoProgress.value < 100) {
        videoProgress.value += 5
      } else {
        clearInterval(videoInterval)
        isPlaying.value = false
        isCompleted.value = true
      }
    }, 200)
  } else {
    if (videoInterval) clearInterval(videoInterval)
  }
}

// Quiz validation
function selectOption(index: number) {
  if (quizChecked.value) return
  selectedAnswer.value = index
}

function checkQuiz() {
  if (selectedAnswer.value === null) return
  quizChecked.value = true
  if (selectedAnswer.value === task.value.quizCorrectIndex) {
    isQuizCorrect.value = true
    isCompleted.value = true
  } else {
    isQuizCorrect.value = false
  }
}

function retryQuiz() {
  selectedAnswer.value = null
  quizChecked.value = false
  isQuizCorrect.value = false
  isCompleted.value = false
}

// PDF navigation
function prevPdfPage() {
  if (pdfPage.value > 1) pdfPage.value--
}

function nextPdfPage() {
  if (task.value.pdfContentPages && pdfPage.value < task.value.pdfContentPages.length) {
    pdfPage.value++
  } else {
    isCompleted.value = true
  }
}

function zoomPdf(amount: number) {
  const newZoom = pdfZoom.value + amount
  if (newZoom >= 50 && newZoom <= 200) {
    pdfZoom.value = newZoom
  }
}

// Claim points action
function claimPoints() {
  authStore.addPoints(task.value.pts)
  claimSuccess.value = true
}

function goBack() {
  router.push('/journey')
}
</script>

<template>
  <div class="task-execution-wrapper">
    <!-- Back Button Header -->
    <div class="task-header">
      <button @click="goBack" class="back-link-btn">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="chevron-icon">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        <span>Back to Journey Map</span>
      </button>
    </div>

    <!-- Main Grid -->
    <div class="task-grid">
      <!-- Player/Reader Panel -->
      <div class="viewer-panel">
        <!-- VIDEO PLAYER -->
        <div v-if="task.type === 'video'" class="video-container">
          <div class="video-mock-screen">
            <div class="video-overlay" v-if="videoProgress === 0 && !isPlaying">
              <button @click="togglePlay" class="play-overlay-btn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="play-svg">
                  <polygon points="5 3 19 12 5 21 5 3"></polygon>
                </svg>
              </button>
            </div>
            <div class="video-playing-state" :class="{ 'playing': isPlaying }">
              <span class="video-icon">🎬</span>
              <span class="video-text">{{ isPlaying ? 'Simulating Playback...' : videoProgress === 100 ? 'Video Finished!' : 'Ready to watch' }}</span>
            </div>
          </div>
          
          <!-- Custom Video Controls -->
          <div class="video-controls">
            <button @click="togglePlay" class="play-btn">
              <svg v-if="!isPlaying" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="control-svg">
                <polygon points="5 3 19 12 5 21 5 3"></polygon>
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="control-svg">
                <line x1="18" y1="5" x2="18" y2="19"></line>
                <line x1="6" y1="5" x2="6" y2="19"></line>
              </svg>
            </button>
            
            <div class="progress-timeline">
              <div class="timeline-bar" :style="{ width: `${videoProgress}%`, backgroundColor: 'var(--primary-color)' }"></div>
            </div>
            
            <span class="duration font-mono">{{ Math.round((videoProgress/100) * 135) }}s / 135s</span>
          </div>
        </div>

        <!-- QUIZ COMPONENT -->
        <div v-else-if="task.type === 'quiz'" class="quiz-container">
          <div class="quiz-card">
            <h3 class="quiz-question">{{ task.quizQuestion }}</h3>
            
            <div class="quiz-options">
              <button 
                v-for="(option, idx) in task.quizOptions" 
                :key="idx"
                @click="selectOption(idx)"
                class="option-card"
                :class="{ 
                  'selected': selectedAnswer === idx,
                  'correct': quizChecked && idx === task.quizCorrectIndex,
                  'incorrect': quizChecked && selectedAnswer === idx && idx !== task.quizCorrectIndex,
                  'disabled': quizChecked
                }"
              >
                <span class="option-num font-mono">{{ String.fromCharCode(65 + idx) }}</span>
                <span class="option-text">{{ option }}</span>
              </button>
            </div>

            <!-- Quiz Result Feedback Banner -->
            <div v-if="quizChecked" class="quiz-feedback" :class="isQuizCorrect ? 'success' : 'fail'">
              <span v-if="isQuizCorrect">🎉 Correct! You unlocked this checkpoint. Click "Claim Points" on the right.</span>
              <div v-else class="fail-feedback">
                <span>❌ Incorrect. Please review security guidelines and try again.</span>
                <button @click="retryQuiz" class="retry-btn">Retry Quiz</button>
              </div>
            </div>

            <button v-else :disabled="selectedAnswer === null" @click="checkQuiz" class="check-quiz-btn">
              Check Answer
            </button>
          </div>
        </div>

        <!-- PDF VIEWER -->
        <div v-else-if="task.type === 'pdf'" class="pdf-container">
          <div class="pdf-toolbar">
            <span class="pdf-filename">{{ task.pdfTitle }}</span>
            <div class="pdf-controls">
              <button @click="zoomPdf(-10)" class="pdf-tool-btn" title="Zoom Out">-</button>
              <span class="pdf-zoom font-mono">{{ pdfZoom }}%</span>
              <button @click="zoomPdf(10)" class="pdf-tool-btn" title="Zoom In">+</button>
            </div>
          </div>
          
          <div class="pdf-page-screen">
            <div class="pdf-paper" :style="{ transform: `scale(${pdfZoom/100})`, transformOrigin: 'top center' }">
              <p class="pdf-text">{{ task.pdfContentPages?.[pdfPage - 1] }}</p>
            </div>
          </div>

          <div class="pdf-footer">
            <button @click="prevPdfPage" :disabled="pdfPage === 1" class="pdf-nav-btn">Previous Page</button>
            <span class="pdf-page font-mono">Page {{ pdfPage }} of {{ task.pdfContentPages?.length }}</span>
            <button @click="nextPdfPage" class="pdf-nav-btn primary">
              {{ pdfPage === task.pdfContentPages?.length ? 'Mark Read' : 'Next Page' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Claim Points / Sidebar -->
      <div class="details-panel">
        <div class="details-card">
          <div class="task-info">
            <span class="task-pts">+{{ task.pts }} Pts</span>
            <h2 class="task-title">{{ task.title }}</h2>
            <p class="task-desc">{{ task.description }}</p>
          </div>

          <div class="status-box" :class="{ 'completed': isCompleted }">
            <div class="status-indicator">
              <span class="indicator-dot"></span>
              <span class="status-text">{{ isCompleted ? 'Task Requirements Satisfied' : 'Incomplete' }}</span>
            </div>
            
            <p v-if="!isCompleted" class="status-instructions">
              {{ task.type === 'video' ? 'Play the welcome video until the end to satisfy requirements.' : 
                 task.type === 'quiz' ? 'Select the correct answer to complete this module checkpoint.' :
                 'Read through all pages of the document setup guide to complete.' }}
            </p>

            <button 
              v-if="isCompleted && !claimSuccess" 
              @click="claimPoints" 
              class="claim-btn animate-pulse"
              :style="{ backgroundColor: 'var(--primary-color)' }"
            >
              Claim +{{ task.pts }} Points
            </button>

            <!-- Claim Success Card -->
            <div v-if="claimSuccess" class="claim-success-banner">
              <div class="success-icon-bg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="tick-icon">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
              </div>
              <h3 class="success-title">Points Claimed!</h3>
              <p class="success-desc">Congratulations! Check your profile level on the top header.</p>
              <button @click="goBack" class="done-btn">Back to Journey Map</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.task-execution-wrapper {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.task-header {
  text-align: left;
}

.back-link-btn {
  background: none;
  border: none;
  color: #94a3b8;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.back-link-btn:hover {
  color: #ffffff;
  background: rgba(255, 255, 255, 0.05);
}

.chevron-icon {
  width: 16px;
  height: 16px;
}

.task-grid {
  display: grid;
  grid-template-columns: 1.8fr 1fr;
  gap: 32px;
  align-items: start;
}

/* Viewer Panel */
.viewer-panel {
  background: rgba(15, 23, 42, 0.5);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  min-height: 480px;
  display: flex;
  flex-direction: column;
}

/* VIDEO PLAYER STYLING */
.video-container {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

.video-mock-screen {
  background: #020617;
  height: 380px;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.video-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2;
}

.play-overlay-btn {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: var(--primary-color);
  border: none;
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 24px rgba(79, 70, 229, 0.4);
  transition: all 0.2s ease;
}

.play-overlay-btn:hover {
  transform: scale(1.1);
  filter: brightness(1.1);
}

.play-svg {
  width: 28px;
  height: 28px;
  margin-left: 4px;
}

.video-playing-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  color: #94a3b8;
}

.video-playing-state.playing {
  animation: pulse 1.5s infinite;
  color: #ffffff;
}

.video-icon {
  font-size: 3rem;
}

.video-text {
  font-size: 1rem;
  font-weight: 500;
}

.video-controls {
  background: #0f172a;
  padding: 16px 24px;
  display: flex;
  align-items: center;
  gap: 16px;
  border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.play-btn {
  background: none;
  border: none;
  color: #ffffff;
  cursor: pointer;
  padding: 6px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.play-btn:hover {
  background: rgba(255, 255, 255, 0.08);
}

.control-svg {
  width: 20px;
  height: 20px;
}

.progress-timeline {
  flex-grow: 1;
  height: 6px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 9999px;
  overflow: hidden;
  position: relative;
}

.timeline-bar {
  height: 100%;
  transition: width 0.2s linear;
}

.duration {
  font-size: 0.8rem;
  color: #94a3b8;
}

/* QUIZ STYLING */
.quiz-container {
  padding: 40px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  flex-grow: 1;
  text-align: left;
}

.quiz-question {
  font-size: 1.25rem;
  font-weight: 600;
  color: #ffffff;
  line-height: 1.4;
  margin: 0 0 28px 0;
}

.quiz-options {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 28px;
}

.option-card {
  background: rgba(255, 255, 255, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 12px;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  cursor: pointer;
  color: #cbd5e1;
  text-align: left;
  transition: all 0.2s ease;
}

.option-card:hover:not(.disabled) {
  background: rgba(255, 255, 255, 0.05);
  border-color: rgba(255, 255, 255, 0.15);
  color: #ffffff;
}

.option-card.selected {
  border-color: var(--primary-color);
  background: rgba(79, 70, 229, 0.08);
  color: #ffffff;
}

.option-card.correct {
  border-color: #10b981;
  background: rgba(16, 185, 129, 0.1);
  color: #34d399;
}

.option-card.incorrect {
  border-color: #ef4444;
  background: rgba(239, 68, 68, 0.1);
  color: #f87171;
}

.option-num {
  font-size: 0.85rem;
  font-weight: 700;
  color: #94a3b8;
  background: rgba(255, 255, 255, 0.05);
  width: 24px;
  height: 24px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.option-card.selected .option-num {
  background: var(--primary-color);
  color: white;
}

.option-card.correct .option-num {
  background: #10b981;
  color: white;
}

.option-card.incorrect .option-num {
  background: #ef4444;
  color: white;
}

.check-quiz-btn {
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  padding: 14px;
  font-weight: 600;
  color: #ffffff;
  cursor: pointer;
  transition: all 0.2s ease;
  width: 100%;
}

.check-quiz-btn:hover:not(:disabled) {
  background: rgba(255, 255, 255, 0.1);
}

.check-quiz-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.quiz-feedback {
  padding: 16px;
  border-radius: 12px;
  font-size: 0.9rem;
  font-weight: 500;
  line-height: 1.5;
}

.quiz-feedback.success {
  background: rgba(16, 185, 129, 0.1);
  border: 1px solid rgba(16, 185, 129, 0.2);
  color: #34d399;
}

.quiz-feedback.fail {
  background: rgba(239, 68, 68, 0.08);
  border: 1px solid rgba(239, 68, 68, 0.15);
  color: #f87171;
}

.fail-feedback {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
}

.retry-btn {
  background: #ef4444;
  border: none;
  color: white;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: opacity 0.2s ease;
}

.retry-btn:hover {
  opacity: 0.9;
}

/* PDF VIEWER STYLING */
.pdf-container {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

.pdf-toolbar {
  background: #0f172a;
  padding: 12px 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.pdf-filename {
  font-size: 0.85rem;
  font-weight: 600;
  color: #cbd5e1;
}

.pdf-controls {
  display: flex;
  align-items: center;
  gap: 12px;
}

.pdf-tool-btn {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.08);
  color: white;
  padding: 2px 8px;
  border-radius: 4px;
  cursor: pointer;
}

.pdf-tool-btn:hover {
  background: rgba(255, 255, 255, 0.1);
}

.pdf-zoom {
  font-size: 0.8rem;
  color: #cbd5e1;
}

.pdf-page-screen {
  background: #334155;
  height: 380px;
  overflow: auto;
  padding: 24px;
  display: flex;
  justify-content: center;
  align-items: flex-start;
}

.pdf-paper {
  background: #ffffff;
  color: #0f172a;
  padding: 32px;
  border-radius: 4px;
  width: 100%;
  max-width: 440px;
  min-height: 280px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
  text-align: left;
  transition: transform 0.2s ease;
}

.pdf-text {
  font-size: 0.95rem;
  line-height: 1.6;
  white-space: pre-line;
  margin: 0;
  font-family: Georgia, serif;
}

.pdf-footer {
  background: #0f172a;
  padding: 12px 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.pdf-nav-btn {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.08);
  color: white;
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.pdf-nav-btn:hover:not(:disabled) {
  background: rgba(255, 255, 255, 0.1);
}

.pdf-nav-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.pdf-nav-btn.primary {
  background: var(--primary-color);
  border-color: transparent;
}

.pdf-nav-btn.primary:hover {
  filter: brightness(1.1);
}

.pdf-page {
  font-size: 0.85rem;
  color: #cbd5e1;
}

/* Sidebar Details Panel */
.details-panel {
  text-align: left;
}

.details-card {
  background: rgba(15, 23, 42, 0.7);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  padding: 32px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
  display: flex;
  flex-direction: column;
  gap: 28px;
}

.task-pts {
  font-size: 0.85rem;
  font-weight: 700;
  color: #fbbf24;
  background: rgba(251, 191, 36, 0.1);
  border: 1px solid rgba(251, 191, 36, 0.2);
  padding: 4px 10px;
  border-radius: 9999px;
  display: inline-block;
  margin-bottom: 12px;
}

.task-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: #ffffff;
  margin: 0 0 10px 0;
}

.task-desc {
  font-size: 0.85rem;
  color: #cbd5e1;
  line-height: 1.6;
  margin: 0;
}

.status-box {
  background: rgba(255, 255, 255, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 16px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.status-indicator {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #94a3b8;
}

.indicator-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #94a3b8;
}

.completed .status-indicator {
  color: #10b981;
}

.completed .indicator-dot {
  background: #10b981;
  box-shadow: 0 0 8px #10b981;
}

.status-instructions {
  font-size: 0.8rem;
  color: #94a3b8;
  line-height: 1.5;
  margin: 0;
}

.claim-btn {
  color: white;
  border: none;
  padding: 14px;
  border-radius: 12px;
  font-size: 0.95rem;
  font-weight: 700;
  cursor: pointer;
  transition: transform 0.2s ease;
  box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
}

.claim-btn:hover {
  transform: translateY(-1px);
  filter: brightness(1.1);
}

.animate-pulse {
  animation: pulse 2s infinite;
}

.claim-success-banner {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 12px;
  padding: 8px 0;
  animation: fadeIn 0.4s ease;
}

.success-icon-bg {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: rgba(16, 185, 129, 0.15);
  border: 2px solid #10b981;
  display: flex;
  align-items: center;
  justify-content: center;
}

.tick-icon {
  width: 20px;
  height: 20px;
  color: #10b981;
}

.success-title {
  font-size: 1.15rem;
  font-weight: 700;
  color: #ffffff;
  margin: 0;
}

.success-desc {
  font-size: 0.8rem;
  color: #94a3b8;
  margin: 0 0 8px 0;
  line-height: 1.4;
}

.done-btn {
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.12);
  color: white;
  padding: 10px 16px;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  width: 100%;
}

.done-btn:hover {
  background: rgba(255, 255, 255, 0.15);
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.8; }
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 1024px) {
  .task-grid {
    grid-template-columns: 1fr;
  }
}
</style>
