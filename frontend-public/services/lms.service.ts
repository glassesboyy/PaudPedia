import type { 
  LmsCertificateResponse, LmsCoursePlayerData, LmsLessonDetail, 
  LmsMarkCompleteResponse, LmsProgress, 
  LmsQuizDetail, LmsQuizSubmitResponse 
} from '~~/types'
import { useApiFetch } from './api/client'
import { API_ENDPOINTS } from './api/endpoints'
import type { ApiResponse } from './api/types'

export const lmsService = {
  async getCoursePlayer(courseSlug: string): Promise<ApiResponse<LmsCoursePlayerData>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.LMS.COURSE(courseSlug))
  },

  async getLessonDetail(courseSlug: string, lessonId: string | number): Promise<ApiResponse<LmsLessonDetail>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.LMS.LESSON(courseSlug, lessonId))
  },

  async markLessonComplete(courseSlug: string, lessonId: string | number): Promise<ApiResponse<LmsMarkCompleteResponse>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.LMS.LESSON_COMPLETE(courseSlug, lessonId), {
      method: 'POST',
    })
  },

  async getProgress(courseSlug: string): Promise<ApiResponse<LmsProgress>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.LMS.PROGRESS(courseSlug))
  },

  async generateCertificate(courseSlug: string): Promise<ApiResponse<LmsCertificateResponse>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.LMS.CERTIFICATE_GENERATE(courseSlug), {
      method: 'POST',
    })
  },

  async downloadCertificateBlob(courseSlug: string): Promise<Blob> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.LMS.CERTIFICATE_DOWNLOAD(courseSlug), {
      responseType: 'blob',
    } as any)
  },

  async getLessonPdfBlob(courseSlug: string, lessonId: string | number): Promise<Blob> {
    const apiFetch = useApiFetch()

    return apiFetch(API_ENDPOINTS.LMS.LESSON_PDF(courseSlug, lessonId), {
      responseType: 'blob',
    } as any)
  },

  async getQuizDetail(courseSlug: string, quizId: string | number): Promise<ApiResponse<LmsQuizDetail>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.LMS.QUIZ_SHOW(courseSlug, quizId))
  },

  async submitQuiz(courseSlug: string, quizId: string | number, payload: { answers: { question_id: number, answer_id: number }[] }): Promise<ApiResponse<LmsQuizSubmitResponse>> {
    const apiFetch = useApiFetch()
    return apiFetch(API_ENDPOINTS.LMS.QUIZ_SUBMIT(courseSlug, quizId), {
      method: 'POST',
      body: payload
    })
  },
}
