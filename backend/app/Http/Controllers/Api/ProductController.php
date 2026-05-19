<?php

namespace App\Http\Controllers\Api;

use App\Contracts\ProductRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $input = [
            'search' => $request->has('search') ? (string) $request->search : null,
            'sort_by' => $request->string('sort_by')->toString(),
            'sort_order' => $request->string('sort_order', 'asc')->toString(),
            'per_page' => max(1, min((int) $request->integer('per_page', 12), 48)),
            'require_featured' => $request->has('featured') && $request->boolean('featured'),
        ];

        if ($request->has('category_id')) {
            $input['category_id'] = $request->category_id;
        }

        $paginated = $this->products->paginatePublicCatalog($input);

        if ($paginated instanceof LengthAwarePaginator) {
            $paginated->through(
                static fn (Product $product) => (new ProductResource($product))->resolve(),
            );
        }

        return response()->json($paginated);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $product = $this->products->findActiveByIdForPublic($id);

        return response()->json((new ProductResource($product))->resolve());
    }
}
