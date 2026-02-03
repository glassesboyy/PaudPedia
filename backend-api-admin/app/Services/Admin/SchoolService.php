<?php

namespace App\Services\Admin;

use App\Models\School;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SchoolService
{
    /**
     * Update school subscription manually
     *
     * @param School $school
     * @param array $data
     * @return School
     * @throws \Exception
     */
    public function updateSchoolSubscription(School $school, array $data): School
    {
        DB::beginTransaction();
        try {
            $oldLogoUrl = $school->logo_url;

            // Handle logo upload if exists
            if (isset($data['logo_url']) && is_file($data['logo_url'])) {
                // Delete old logo
                if ($oldLogoUrl) {
                    Storage::disk('public')->delete($oldLogoUrl);
                }
                
                $data['logo_url'] = $data['logo_url']->store('schools', 'public');
            }

            // Update limits based on subscription plan if changed
            if (isset($data['subscription_plan'])) {
                $plan = $data['subscription_plan'];
                $data['student_limit'] = $plan->studentLimit() ?? 9999;
                $data['teacher_limit'] = $plan->teacherLimit() ?? 9999;
            }

            $school->update($data);

            DB::commit();

            return $school->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete new uploaded logo if exists
            if (isset($data['logo_url']) && is_string($data['logo_url']) && $data['logo_url'] !== $oldLogoUrl) {
                Storage::disk('public')->delete($data['logo_url']);
            }
            
            throw $e;
        }
    }

    /**
     * Toggle school active status
     *
     * @param School $school
     * @return School
     * @throws \Exception
     */
    public function toggleActiveStatus(School $school): School
    {
        DB::beginTransaction();
        try {
            $school->is_active = !$school->is_active;
            $school->save();

            DB::commit();

            return $school->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get school statistics
     *
     * @param School $school
     * @return array
     */
    public function getSchoolStatistics(School $school): array
    {
        return [
            'total_students' => $school->students()->count(),
            'active_students' => $school->students()->where('status', 'active')->count(),
            'total_teachers' => $school->teachers()->count(),
            'total_classes' => $school->classes()->count(),
            'remaining_student_slots' => $school->getRemainingStudentSlots(),
            'remaining_teacher_slots' => $school->getRemainingTeacherSlots(),
            'is_pro' => $school->isPro(),
            'subscription_expired' => $school->subscription_expires_at && $school->subscription_expires_at->isPast(),
        ];
    }
}
