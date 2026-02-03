<?php

namespace App\Services\Content;

use App\Models\Testimonial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TestimonialService
{
    /**
     * Create a new testimonial
     *
     * @param array $data
     * @return Testimonial
     * @throws \Exception
     */
    public function createTestimonial(array $data): Testimonial
    {
        DB::beginTransaction();
        try {
            // Handle photo upload if exists
            if (isset($data['photo_url']) && is_file($data['photo_url'])) {
                $data['photo_url'] = $data['photo_url']->store('testimonials', 'public');
            }

            $testimonial = Testimonial::create($data);

            DB::commit();

            return $testimonial->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded photo if exists
            if (isset($data['photo_url']) && is_string($data['photo_url'])) {
                Storage::disk('public')->delete($data['photo_url']);
            }
            
            throw $e;
        }
    }

    /**
     * Update an existing testimonial
     *
     * @param Testimonial $testimonial
     * @param array $data
     * @return Testimonial
     * @throws \Exception
     */
    public function updateTestimonial(Testimonial $testimonial, array $data): Testimonial
    {
        DB::beginTransaction();
        try {
            $oldPhotoUrl = $testimonial->photo_url;

            // Handle photo upload if exists
            if (isset($data['photo_url']) && is_file($data['photo_url'])) {
                // Delete old photo
                if ($oldPhotoUrl) {
                    Storage::disk('public')->delete($oldPhotoUrl);
                }
                
                $data['photo_url'] = $data['photo_url']->store('testimonials', 'public');
            }

            $testimonial->update($data);

            DB::commit();

            return $testimonial->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete new uploaded photo if exists
            if (isset($data['photo_url']) && is_string($data['photo_url']) && $data['photo_url'] !== $oldPhotoUrl) {
                Storage::disk('public')->delete($data['photo_url']);
            }
            
            throw $e;
        }
    }

    /**
     * Delete testimonial
     *
     * @param Testimonial $testimonial
     * @return bool
     * @throws \Exception
     */
    public function deleteTestimonial(Testimonial $testimonial): bool
    {
        DB::beginTransaction();
        try {
            $photoUrl = $testimonial->photo_url;

            $testimonial->delete();

            // Delete photo if exists
            if ($photoUrl) {
                Storage::disk('public')->delete($photoUrl);
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Toggle approval status
     *
     * @param Testimonial $testimonial
     * @return Testimonial
     * @throws \Exception
     */
    public function toggleApprovalStatus(Testimonial $testimonial): Testimonial
    {
        DB::beginTransaction();
        try {
            $testimonial->is_approved = !$testimonial->is_approved;
            
            // Jika approval dibatalkan (is_approved = false), auto-uncheck featured
            if (!$testimonial->is_approved && $testimonial->is_featured) {
                $testimonial->is_featured = false;
            }
            
            $testimonial->save();

            DB::commit();

            return $testimonial->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Toggle featured status
     *
     * @param Testimonial $testimonial
     * @return Testimonial
     * @throws \Exception
     */
    public function toggleFeaturedStatus(Testimonial $testimonial): Testimonial
    {
        DB::beginTransaction();
        try {
            // Validasi: tidak bisa jadikan featured jika belum approved (kecuali untuk disable featured)
            if (!$testimonial->is_featured && !$testimonial->is_approved) {
                throw new \Exception('Testimoni harus disetujui terlebih dahulu sebelum dapat dijadikan unggulan.');
            }

            $testimonial->is_featured = !$testimonial->is_featured;
            $testimonial->save();

            DB::commit();

            return $testimonial->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get testimonial statistics
     *
     * @param Testimonial $testimonial
     * @return array
     */
    public function getTestimonialStatistics(Testimonial $testimonial): array
    {
        return [
            'is_approved' => $testimonial->is_approved,
            'is_featured' => $testimonial->is_featured,
            'rating' => $testimonial->rating,
            'star_rating' => $testimonial->star_rating,
        ];
    }
}
