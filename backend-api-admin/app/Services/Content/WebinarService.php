<?php

namespace App\Services\Content;

use App\Models\Webinar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class WebinarService
{
    /**
     * Create a new webinar
     *
     * @param array $data
     * @return Webinar
     * @throws \Exception
     */
    public function createWebinar(array $data): Webinar
    {
        DB::beginTransaction();
        try {
            // Generate slug if not provided
            if (!isset($data['slug']) || empty($data['slug'])) {
                $data['slug'] = $this->generateUniqueSlug($data['title']);
            }

            // Create webinar
            $webinar = Webinar::create($data);

            DB::commit();

            return $webinar->load('mentor');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing webinar
     *
     * @param Webinar $webinar
     * @param array $data
     * @return Webinar
     * @throws \Exception
     */
    public function updateWebinar(Webinar $webinar, array $data): Webinar
    {
        DB::beginTransaction();
        try {
            // Update slug if title changed
            if (isset($data['title']) && $data['title'] !== $webinar->title) {
                $data['slug'] = $this->generateUniqueSlug($data['title'], $webinar->id);
            }

            // Update webinar
            $webinar->update($data);

            DB::commit();

            return $webinar->fresh('mentor');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a webinar (soft delete)
     *
     * @param Webinar $webinar
     * @return bool
     * @throws \Exception
     */
    public function deleteWebinar(Webinar $webinar): bool
    {
        DB::beginTransaction();
        try {
            $deleted = $webinar->delete();

            DB::commit();

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Bulk delete webinars
     *
     * @param array $ids
     * @return int Number of deleted records
     * @throws \Exception
     */
    public function bulkDeleteWebinars(array $ids): int
    {
        DB::beginTransaction();
        try {
            $count = Webinar::whereIn('id', $ids)->delete();

            DB::commit();

            return $count;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Restore a soft-deleted webinar
     *
     * @param int $id
     * @return Webinar|null
     * @throws \Exception
     */
    public function restoreWebinar(int $id): ?Webinar
    {
        DB::beginTransaction();
        try {
            $webinar = Webinar::withTrashed()->find($id);
            
            if ($webinar && $webinar->trashed()) {
                $webinar->restore();
            }

            DB::commit();

            return $webinar?->load('mentor');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Force delete a webinar (permanent)
     *
     * @param Webinar $webinar
     * @return bool
     * @throws \Exception
     */
    public function forceDeleteWebinar(Webinar $webinar): bool
    {
        DB::beginTransaction();
        try {
            $deleted = $webinar->forceDelete();

            DB::commit();

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get upcoming webinars
     *
     * @return Collection
     */
    public function getUpcomingWebinars(): Collection
    {
        return Webinar::with('mentor')
            ->upcoming()
            ->active()
            ->orderBy('scheduled_at', 'asc')
            ->get();
    }

    /**
     * Get past webinars
     *
     * @return Collection
     */
    public function getPastWebinars(): Collection
    {
        return Webinar::with('mentor')
            ->past()
            ->orderBy('scheduled_at', 'desc')
            ->get();
    }

    /**
     * Toggle webinar active status
     *
     * @param Webinar $webinar
     * @return Webinar
     */
    public function toggleActiveStatus(Webinar $webinar): Webinar
    {
        $webinar->update(['is_active' => !$webinar->is_active]);
        
        return $webinar->fresh();
    }

    /**
     * Generate unique slug for webinar
     *
     * @param string $title
     * @param int|null $excludeId
     * @return string
     */
    protected function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        // Check if slug exists
        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Check if slug exists
     *
     * @param string $slug
     * @param int|null $excludeId
     * @return bool
     */
    protected function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = Webinar::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Get webinar statistics
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'total' => Webinar::count(),
            'active' => Webinar::active()->count(),
            'upcoming' => Webinar::upcoming()->active()->count(),
            'past' => Webinar::past()->count(),
            'total_revenue' => $this->calculateTotalRevenue(),
        ];
    }

    /**
     * Calculate total revenue from webinar sales
     *
     * @return float
     */
    protected function calculateTotalRevenue(): float
    {
        return Webinar::withCount(['orderItems' => function ($query) {
            $query->whereHas('order', function ($q) {
                $q->where('payment_status', 'paid');
            });
        }])
        ->get()
        ->sum(function ($webinar) {
            return $webinar->price * $webinar->order_items_count;
        });
    }
}
