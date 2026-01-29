<?php

namespace App\Services\Content;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;




class CategoryService {

    /**
     * Check if category can be deleted (tidak boleh ada relasi kursus, produk, artikel)
     */
    public function canBeDeleted(Category $category): bool
    {
        $totalCourses = $category->courses()->count();
        $totalProducts = $category->products()->count();
        $totalArticles = $category->articles()->count();
        return $totalCourses === 0 && $totalProducts === 0 && $totalArticles === 0;
    }

    /**
     * Get detailed message about related content (untuk notifikasi gagal hapus)
     */
    public function getRelatedContentMessage(Category $category): string
    {
        $totalCourses = $category->courses()->count();
        $totalProducts = $category->products()->count();
        $totalArticles = $category->articles()->count();
        $parts = [];
        if ($totalCourses > 0) {
            $parts[] = "$totalCourses kursus";
        }
        if ($totalProducts > 0) {
            $parts[] = "$totalProducts produk";
        }
        if ($totalArticles > 0) {
            $parts[] = "$totalArticles artikel";
        }
        $content = implode(', ', $parts);
        return "Kategori tidak dapat dihapus karena masih terhubung dengan $content.";
    }
    /**
     * Create a new category
     *
     * @param array $data
     * @return Category
     * @throws \Exception
     */
    public function createCategory(array $data): Category
    {
        DB::beginTransaction();
        try {
            // Generate slug if not provided
            if (!isset($data['slug']) || empty($data['slug'])) {
                $data['slug'] = $this->generateUniqueSlug($data['name']);
            }

            // Create category
            $category = Category::create($data);

            DB::commit();

            return $category->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing category
     *
     * @param Category $category
     * @param array $data
     * @return Category
     * @throws \Exception
     */
    public function updateCategory(Category $category, array $data): Category
    {
        DB::beginTransaction();
        try {
            // Update slug if name changed
            if (isset($data['name']) && $data['name'] !== $category->name) {
                if (!isset($data['slug']) || empty($data['slug'])) {
                    $data['slug'] = $this->generateUniqueSlug($data['name'], $category->id);
                }
            }

            $category->update($data);

            DB::commit();

            return $category->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete category
     *
     * @param Category $category
     * @return bool
     * @throws \Exception
     */
    public function deleteCategory(Category $category): bool
    {
        DB::beginTransaction();
        try {
            // Check if category has related records
            $hasRelations = $category->courses()->exists() 
                || $category->products()->exists() 
                || $category->articles()->exists();

            if ($hasRelations) {
                throw new \Exception('Kategori tidak dapat dihapus karena masih memiliki data terkait (kursus, produk, atau artikel).');
            }

            $category->delete();
            
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Generate unique slug
     *
     * @param string $name
     * @param int|null $ignoreId
     * @return string
     */
    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug exists
     *
     * @param string $slug
     * @param int|null $ignoreId
     * @return bool
     */
    private function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $query = Category::where('slug', $slug);
        
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        
        return $query->exists();
    }

    /**
     * Get category statistics
     *
     * @param Category $category
     * @return array
     */
    public function getCategoryStatistics(Category $category): array
    {
        return [
            'total_courses' => $category->courses()->count(),
            'total_products' => $category->products()->count(),
            'total_articles' => $category->articles()->count(),
        ];
    }
}
