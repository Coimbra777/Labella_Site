<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private const ALLOWED_SORT_COLUMNS = [
        'created_at',
        'name',
        'price',
        'sort_order',
    ];

    /**
     * Transform product images to full URLs.
     */
    private function transformProductImages(Product $product): array
    {
        $data = $product->toArray();
        $images = $product->images ?? [];

        if (is_array($images) && !empty($images)) {
            $data['images'] = array_map(function ($path) {
                if (empty($path) || str_starts_with($path, 'http')) {
                    return $path;
                }
                return Storage::disk('public')->url($path);
            }, $images);
            $data['main_image'] = $data['images'][0] ?? null;
        } else {
            $data['main_image'] = null;
        }

        return $data;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('category')
            ->where('is_active', true);

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by featured
        if ($request->has('featured') && $request->featured) {
            $query->where('is_featured', true);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sortBy = $request->string('sort_by')->toString();
        $sortBy = in_array($sortBy, self::ALLOWED_SORT_COLUMNS, true) ? $sortBy : 'sort_order';

        $sortOrder = strtolower($request->string('sort_order', 'asc')->toString());
        $sortOrder = in_array($sortOrder, ['asc', 'desc'], true) ? $sortOrder : 'asc';
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = max(1, min((int) $request->integer('per_page', 12), 48));
        $paginated = $query->paginate($perPage);

        // Transform products with full image URLs
        $paginated->getCollection()->transform(function ($product) {
            return $this->transformProductImages($product);
        });

        return response()->json($paginated);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $product = Product::with('category')
            ->where('is_active', true)
            ->findOrFail($id);

        return response()->json($this->transformProductImages($product));
    }
}
