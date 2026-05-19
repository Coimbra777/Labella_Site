<?php

namespace App\Repositories;

use App\Contracts\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentProductRepository implements ProductRepositoryInterface
{
    private const PUBLIC_SORT_COLUMNS = [
        'created_at',
        'name',
        'price',
        'sort_order',
    ];

    private const ADMIN_SORT_COLUMNS = [
        'created_at',
        'name',
        'price',
        'quantity',
        'sort_order',
    ];

    public function paginatePublicCatalog(array $input): LengthAwarePaginator
    {
        $query = Product::with('category')
            ->where('is_active', true);

        if (array_key_exists('category_id', $input)) {
            $query->where('category_id', $input['category_id']);
        }

        if (! empty($input['require_featured'])) {
            $query->where('is_featured', true);
        }

        if (! empty($input['search'])) {
            $query->where('name', 'like', '%'.$input['search'].'%');
        }

        $sortBy = (string) ($input['sort_by'] ?? '');
        $sortBy = in_array($sortBy, self::PUBLIC_SORT_COLUMNS, true) ? $sortBy : 'sort_order';

        $sortOrder = strtolower((string) ($input['sort_order'] ?? 'asc'));
        $sortOrder = in_array($sortOrder, ['asc', 'desc'], true) ? $sortOrder : 'asc';
        $query->orderBy($sortBy, $sortOrder);

        $perPage = (int) ($input['per_page'] ?? 12);
        $perPage = max(1, min($perPage, 48));

        return $query->paginate($perPage);
    }

    public function findActiveByIdForPublic(int|string $id): Product
    {
        return Product::with('category')
            ->where('is_active', true)
            ->findOrFail($id);
    }

    public function paginateAdminList(array $input): LengthAwarePaginator
    {
        $query = Product::with('category');

        if (! empty($input['search'])) {
            $search = (string) $input['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (array_key_exists('category_id', $input) && $input['category_id'] !== null && $input['category_id'] !== '') {
            $query->where('category_id', $input['category_id']);
        }

        if (array_key_exists('is_active', $input) && $input['is_active'] !== null && $input['is_active'] !== '') {
            $query->where('is_active', $input['is_active']);
        }

        $sortBy = (string) ($input['sort_by'] ?? '');
        $sortBy = in_array($sortBy, self::ADMIN_SORT_COLUMNS, true) ? $sortBy : 'created_at';

        $sortOrder = strtolower((string) ($input['sort_order'] ?? 'desc'));
        $sortOrder = in_array($sortOrder, ['asc', 'desc'], true) ? $sortOrder : 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $perPage = (int) ($input['per_page'] ?? 15);
        $perPage = max(1, min($perPage, 100));

        return $query->paginate($perPage);
    }

    public function findWithCategoryForAdmin(int|string $id): Product
    {
        return Product::with('category')->findOrFail($id);
    }
}
