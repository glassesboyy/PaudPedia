<?php

namespace App\Services\Content;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * Create a new product
     *
     * @param array $data
     * @return Product
     * @throws \Exception
     */
    public function createProduct(array $data): Product
    {
        DB::beginTransaction();
        try {
            // Generate slug if not provided
            if (!isset($data['slug']) || empty($data['slug'])) {
                $data['slug'] = $this->generateUniqueSlug($data['title']);
            }

            // Create product
            $product = Product::create($data);

            DB::commit();

            return $product->load('category');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing product
     *
     * @param Product $product
     * @param array $data
     * @return Product
     * @throws \Exception
     */
    public function updateProduct(Product $product, array $data): Product
    {
        DB::beginTransaction();
        try {
            // Update slug if title changed
            if (isset($data['title']) && $data['title'] !== $product->title) {
                if (!isset($data['slug']) || empty($data['slug'])) {
                    $data['slug'] = $this->generateUniqueSlug($data['title'], $product->id);
                }
            }

            $product->update($data);

            DB::commit();

            return $product->fresh('category');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete product (soft delete)
     *
     * @param Product $product
     * @return bool
     * @throws \Exception
     */
    public function deleteProduct(Product $product): bool
    {
        DB::beginTransaction();
        try {
            $product->delete();
            
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Restore deleted product
     *
     * @param Product $product
     * @return Product
     * @throws \Exception
     */
    public function restoreProduct(Product $product): Product
    {
        DB::beginTransaction();
        try {
            $product->restore();
            
            DB::commit();
            
            return $product->fresh('category');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Force delete product permanently
     *
     * @param Product $product
     * @return bool
     * @throws \Exception
     */
    public function forceDeleteProduct(Product $product): bool
    {
        DB::beginTransaction();
        try {
            $product->forceDelete();
            
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Toggle product active status
     *
     * @param Product $product
     * @return Product
     * @throws \Exception
     */
    public function toggleActiveStatus(Product $product): Product
    {
        DB::beginTransaction();
        try {
            $product->is_active = !$product->is_active;
            $product->save();

            DB::commit();

            return $product->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Generate unique slug
     *
     * @param string $title
     * @param int|null $ignoreId
     * @return string
     */
    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
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
        $query = Product::where('slug', $slug);
        
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        
        return $query->exists();
    }

    /**
     * Get product statistics
     *
     * @param Product $product
     * @return array
     */
    public function getProductStatistics(Product $product): array
    {
        return [
            'total_purchases' => $product->total_purchases,
            'has_discount' => $product->hasDiscount(),
            'discount_percentage' => $product->discount_percentage,
        ];
    }
}
