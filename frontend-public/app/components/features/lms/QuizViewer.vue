<script setup lang="ts">
import { ref, computed } from 'vue'
import { lmsService } from '~~/services'
import type { LmsQuizDetail } from '~~/types'
import { useLmsStore } from '~~/stores/lms'

const props = defineProps<{
  quiz: LmsQuizDetail
}>()

const emit = defineEmits<{
  (e: 'submitted'): void
}>()

const store = useLmsStore()
const toast = useToast()

// State
const isSubmitting = ref(false)
const selectedAnswers = ref<Record<number, number>>({}) // Record<question_id, answer_id>
const submitError = ref('')

// Modes
const isQuizTakingMode = ref(false)
const isQuizReviewMode = ref(false)

// Randomization & Stepper
const shuffledQuestions = ref<any[]>([])
const currentStepIndex = ref(0)
const currentQuestion = computed(() => shuffledQuestions.value[currentStepIndex.value])
const isLastQuestion = computed(() => currentStepIndex.value === props.quiz.total_questions - 1)
const isFirstQuestion = computed(() => currentStepIndex.value === 0)

const allQuestionsAnswered = computed(() => {
  return props.quiz.questions.every(q => selectedAnswers.value[q.id])
})

const quizProgressPercentage = computed(() => {
  if (isQuizReviewMode.value) return 100
  const answeredCount = Object.keys(selectedAnswers.value).length
  if (props.quiz.total_questions === 0) return 0
  return Math.round((answeredCount / props.quiz.total_questions) * 100)
})

function shuffleArray(array: any[]) {
  const newArr = [...array]
  for (let i = newArr.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [newArr[i], newArr[j]] = [newArr[j], newArr[i]]
  }
  return newArr
}

function startQuiz() {
  selectedAnswers.value = {}
  submitError.value = ''
  currentStepIndex.value = 0
  shuffledQuestions.value = shuffleArray(props.quiz.questions)
  isQuizTakingMode.value = true
  isQuizReviewMode.value = false
}

function startReview() {
  if (!props.quiz.latest_attempt) return
  currentStepIndex.value = 0
  shuffledQuestions.value = shuffleArray(props.quiz.questions)
  isQuizReviewMode.value = true
  isQuizTakingMode.value = false
}

function stopMode() {
  isQuizTakingMode.value = false
  isQuizReviewMode.value = false
}

function nextStep() {
  if (!isLastQuestion.value) currentStepIndex.value++
}

function prevStep() {
  if (!isFirstQuestion.value) currentStepIndex.value--
}

async function submitQuiz() {
  if (!allQuestionsAnswered.value) {
    submitError.value = 'Mohon jawab seluruh pertanyaan sebelum mengakhiri kuis.'
    return
  }

  isSubmitting.value = true
  submitError.value = ''

  try {
    const payload = {
      answers: Object.entries(selectedAnswers.value).map(([qId, aId]) => ({
        question_id: Number(qId),
        answer_id: Number(aId)
      }))
    }

    const res = await lmsService.submitQuiz(store.courseSlug, props.quiz.quiz_id, payload)
    
    isQuizTakingMode.value = false
    toast.success('Kuis berhasil diselesaikan!')
    emit('submitted') // This will auto-fetch the updated quiz latest_attempt data
  } catch (err: any) {
    submitError.value = err.response?._data?.message || 'Gagal mengirimkan jawaban kuis. Silakan coba lagi.'
  } finally {
    isSubmitting.value = false
  }
}

// Helpers for Review Mode
function getReviewAnswerStatus(questionId: number, answerId: number) {
  if (!isQuizReviewMode.value || !props.quiz.latest_attempt) return 'idle'
  
  const attemptAns = props.quiz.latest_attempt.answers.find(a => a.question_id === questionId)
  if (!attemptAns) return 'idle'

  if (attemptAns.correct_answer_id === answerId) {
    return 'correct'
  }
  
  if (attemptAns.selected_answer_id === answerId && !attemptAns.is_correct) {
    return 'wrong'
  }

  return 'idle'
}

function getReviewQuestionStatus(questionId: number) {
  if (!isQuizReviewMode.value || !props.quiz.latest_attempt) return 'unanswered'
  const attemptAns = props.quiz.latest_attempt.answers.find(a => a.question_id === questionId)
  if (!attemptAns) return 'unanswered'
  if (attemptAns.is_correct) return 'correct'
  return 'wrong'
}
</script>

<template>
  <div class="h-full bg-surface">
    <!-- Quiz Taking / Review Stepper Interface -->
    <div v-if="isQuizTakingMode || isQuizReviewMode" class="max-w-3xl mx-auto p-4 md:p-8 space-y-6 md:space-y-8 py-6 md:py-10">
      
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-border pb-4 gap-4">
        <div>
          <h2 class="text-xl font-bold text-heading flex items-center gap-2">
            <span v-if="isQuizReviewMode" class="bg-primary-100 text-primary-700 px-2 py-0.5 rounded text-sm shrink-0">Mode Review</span>
            {{ quiz.title }}
          </h2>
          <p v-if="isQuizTakingMode" class="text-sm text-muted mt-1">Soal {{ currentStepIndex + 1 }} dari {{ quiz.total_questions }}</p>
          <p v-if="isQuizReviewMode" class="text-sm text-muted mt-1">Melihat jawaban pada percobaan terakhir Anda.</p>
        </div>
        
        <div class="flex items-center gap-2">
           <!-- Progress bar track -->
          <div class="w-32 h-2.5 bg-surface-sunken rounded-full overflow-hidden border border-border">
            <div 
              class="h-full bg-primary-500 transition-all duration-300 ease-out"
              :style="{ width: `${quizProgressPercentage}%` }"
            ></div>
          </div>
          <span class="text-xs font-semibold text-primary-600 min-w-[2rem] text-right">{{ quizProgressPercentage }}%</span>
        </div>
      </div>

      <!-- Question Navigator -->
      <div class="mb-6 p-4 md:p-6 rounded-2xl border border-border bg-surface shadow-sm">
        <h3 class="text-sm font-bold text-heading mb-3 flex items-center gap-2">
          <Icon name="lucide:layout-grid" class="w-4 h-4 text-primary-500" />
          Navigasi Soal
        </h3>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="(q, idx) in shuffledQuestions"
            :key="q.id"
            @click="currentStepIndex = idx"
            class="w-10 h-10 rounded-xl font-bold text-sm flex items-center justify-center transition-all border"
            :class="[
              currentStepIndex === idx ? 'ring-2 ring-primary-500 ring-offset-2 dark:ring-offset-slate-900 border-primary-500' : '',
              isQuizReviewMode 
                ? (getReviewQuestionStatus(q.id) === 'correct' ? 'bg-success-100 text-success-800 border-success-200' : (getReviewQuestionStatus(q.id) === 'wrong' ? 'bg-danger-100 text-danger-800 border-danger-200' : 'bg-surface-sunken text-muted border-border'))
                : (selectedAnswers[q.id] ? 'bg-primary-50 text-primary-700 border-primary-200' : 'bg-surface text-muted border-border hover:bg-surface-sunken')
            ]"
          >
            {{ idx + 1 }}
          </button>
        </div>
      </div>

      <!-- Question Card -->
      <Transition name="fade-slide" mode="out-in">
        <div v-if="currentQuestion" :key="currentQuestion.id" class="p-5 md:p-8 rounded-2xl border border-border bg-surface shadow-sm">
          <p class="font-medium text-body leading-relaxed md:text-lg mb-6">{{ currentQuestion.question }}</p>
          
          <div class="space-y-3">
            <label 
              v-for="answer in currentQuestion.answers" 
              :key="answer.id"
              class="flex items-center gap-4 p-4 rounded-xl border transition-all duration-200"
              :class="[
                isQuizReviewMode ? 'cursor-default' : 'cursor-pointer hover:bg-surface-sunken hover:border-border-muted',
                
                // Taking Mode Selected State
                isQuizTakingMode && selectedAnswers[currentQuestion.id] === answer.id ? 'ring-2 ring-primary-500 border-primary-500 bg-primary-50/20' : '',
                !isQuizTakingMode && !isQuizReviewMode ? 'border-border bg-surface' : '',

                // Review Mode States
                getReviewAnswerStatus(currentQuestion.id, answer.id) === 'correct' ? 'border-success-500 bg-success-50/50 ring-2 ring-success-500/50' : '',
                getReviewAnswerStatus(currentQuestion.id, answer.id) === 'wrong' ? 'border-danger-500 bg-danger-50/50' : '',
                isQuizReviewMode && getReviewAnswerStatus(currentQuestion.id, answer.id) === 'idle' ? 'border-border/50 opacity-50 bg-surface-sunken' : '',
              ]"
            >
              <div class="relative flex items-center justify-center">
                <input 
                  type="radio" 
                  :name="`question_${currentQuestion.id}`" 
                  :value="answer.id"
                  v-model="selectedAnswers[currentQuestion.id]"
                  :disabled="isQuizReviewMode"
                  class="w-5 h-5 text-primary-600 border-border-muted focus:ring-primary-500 transition-all disabled:opacity-0"
                  :class="{ 'opacity-0 absolute scale-0': isQuizReviewMode }"
                />
                <Icon 
                  v-if="isQuizReviewMode && getReviewAnswerStatus(currentQuestion.id, answer.id) === 'correct'"
                  name="lucide:check-circle-2" class="w-6 h-6 text-success-600 absolute" 
                />
                <Icon 
                  v-if="isQuizReviewMode && getReviewAnswerStatus(currentQuestion.id, answer.id) === 'wrong'"
                  name="lucide:x-circle" class="w-6 h-6 text-danger-500 absolute" 
                />
              </div>

              <span class="text-sm md:text-base text-body leading-snug flex-1"
                :class="{ 
                  'font-semibold text-success-700': getReviewAnswerStatus(currentQuestion.id, answer.id) === 'correct',
                  'line-through text-danger-700': getReviewAnswerStatus(currentQuestion.id, answer.id) === 'wrong'
                }"
              >
                {{ answer.answer }}
              </span>
            </label>
          </div>
        </div>
      </Transition>

      <!-- Error message -->
      <div v-if="submitError" class="p-4 rounded-xl bg-danger-50 border border-danger-200 text-danger-600 text-sm flex items-center gap-2">
        <Icon name="lucide:alert-circle" class="w-4 h-4 shrink-0" />
        {{ submitError }}
      </div>

      <!-- Navigation -->
      <div class="flex items-center justify-between pt-6 mt-4">
        <UButton variant="outline" icon="lucide:arrow-left" :disabled="isFirstQuestion" @click="prevStep">
          Sebelumnya
        </UButton>
        
        <div class="flex items-center gap-3">
          <UButton variant="ghost" color="gray" @click="stopMode" class="hidden sm:inline-flex">
            Keluar
          </UButton>
          
          <UButton 
            v-if="!isLastQuestion" 
            variant="primary" 
            icon="lucide:arrow-right"
            trailing
            @click="nextStep"
          >
            Selanjutnya
          </UButton>

          <UButton 
            v-else-if="isQuizTakingMode" 
            variant="primary" 
            icon="lucide:check"
            trailing
            :loading="isSubmitting" 
            :disabled="!allQuestionsAnswered"
            @click="submitQuiz"
          >
            Selesaikan Kuis
          </UButton>

          <UButton 
            v-else-if="isQuizReviewMode && isLastQuestion" 
            variant="primary" 
            icon="lucide:arrow-left-to-line"
            @click="stopMode"
          >
            Tutup Review
          </UButton>
        </div>
      </div>
      <div class="text-center mt-4 sm:hidden">
        <UButton variant="ghost" color="gray" @click="stopMode" class="text-xs">Kembali ke Beranda Kuis</UButton>
      </div>

    </div>

    <!-- Quiz Welcome / Result Screen -->
    <div v-else class="h-full flex flex-col items-center justify-center p-6 text-center">
      <div v-if="quiz.is_passed && quiz.attempt_count > 0" class="w-20 h-20 rounded-full bg-success-100 flex items-center justify-center mb-6 ring-[12px] ring-success-50 scale-in-center">
        <Icon name="lucide:badge-check" class="w-10 h-10 text-success-600" />
      </div>
      <div v-else-if="!quiz.is_passed && quiz.attempt_count > 0" class="w-20 h-20 rounded-full bg-danger-100 flex items-center justify-center mb-6 ring-[12px] ring-danger-50 scale-in-center">
        <Icon name="lucide:x-octagon" class="w-10 h-10 text-danger-600" />
      </div>
      <div v-else class="w-20 h-20 rounded-full bg-primary-100 flex items-center justify-center mb-6 ring-[12px] ring-primary-50">
        <Icon name="lucide:book-open-check" class="w-10 h-10 text-primary-600" />
      </div>
      
      <h2 class="text-2xl md:text-3xl font-bold text-heading mb-3">{{ quiz.title }}</h2>
      <p v-if="quiz.description" class="text-muted max-w-lg mb-8 text-sm md:text-base">{{ quiz.description }}</p>

      <div class="flex flex-wrap justify-center items-stretch gap-4 md:gap-6 mb-8 w-full max-w-2xl">
        <div class="flex-1 min-w-[120px] flex flex-col items-center p-4 bg-surface-sunken border border-border rounded-2xl shadow-sm">
          <span class="text-muted text-xs font-semibold uppercase tracking-wider mb-2">Total Soal</span>
          <span class="font-bold text-2xl text-heading">{{ quiz.total_questions }}</span>
        </div>
        <div class="flex-1 min-w-[120px] flex flex-col items-center p-4 bg-surface-sunken border border-border rounded-2xl shadow-sm">
          <span class="text-muted text-xs font-semibold uppercase tracking-wider mb-2">Skor Tertinggi</span>
          <span class="font-bold text-2xl" :class="quiz.best_score && quiz.best_score >= 70 ? 'text-success-600' : (quiz.best_score === null ? 'text-heading' : 'text-danger-600')">
            {{ quiz.best_score ?? '-' }}
          </span>
        </div>
        <div class="flex-1 min-w-[120px] flex flex-col items-center p-4 rounded-2xl border shadow-sm transition-colors"
          :class="quiz.attempt_count > 0 ? (quiz.is_passed ? 'bg-success-50 border-success-200' : 'bg-danger-50 border-danger-200') : 'bg-surface-sunken border-border'"
        >
          <span class="text-muted text-xs font-semibold uppercase tracking-wider mb-2" :class="quiz.attempt_count > 0 ? (quiz.is_passed ? 'text-success-700' : 'text-danger-700') : ''">Status</span>
          <span class="font-bold text-lg leading-tight" :class="quiz.attempt_count > 0 ? (quiz.is_passed ? 'text-success-700' : 'text-danger-700') : 'text-heading'">
            {{ quiz.attempt_count > 0 ? (quiz.is_passed ? 'LULUS' : 'TIDAK LULUS') : 'BELUM DIAMBIL' }}
          </span>
        </div>
      </div>

      <div class="max-w-2xl w-full p-5 border border-primary-200 rounded-2xl bg-primary-50/50 text-left mb-8">
        <h4 class="font-bold text-primary-900 mb-3 flex items-center gap-2">
          <Icon name="lucide:info" class="w-5 h-5 text-primary-600" /> Petunjuk Evaluasi
        </h4>
        <ul class="text-sm text-primary-800 space-y-2.5 list-disc pl-5">
          <li>Anda harus memperoleh nilai <strong>minimal 70</strong> untuk lulus.</li>
          <li>Anda diizinkan mengulang kuis berulang kali untuk memperbaiki nilai dan status kelulusan.</li>
        </ul>
      </div>

      <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full max-w-2xl">
        <UButton size="lg" variant="primary" class="w-full shadow-md" @click="startQuiz">
          <Icon name="lucide:play" class="w-4 h-4 mr-1.5 hidden sm:inline-block" />
          {{ quiz.attempt_count > 0 ? 'Mulai Ulang Kuis' : 'Mulai Kuis Sekarang' }}
        </UButton>
        <UButton v-if="quiz.latest_attempt" size="lg" variant="outline" class="w-full bg-surface hover:bg-surface-sunken" @click="startReview">
          <Icon name="lucide:search-check" class="w-4 h-4 mr-1.5 hidden sm:inline-block" />
          Cek Jawaban
        </UButton>
      </div>
    </div>

  </div>
</template>

<style scoped>
/* Simple enter/leave animation for stepper transition */
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateX(10px) scale(0.98);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateX(-10px) scale(0.98);
}

.scale-in-center {
  animation: scale-in-center 0.5s cubic-bezier(0.250, 0.460, 0.450, 0.940) both;
}

@keyframes scale-in-center {
  0% { transform: scale(0); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}
</style>
