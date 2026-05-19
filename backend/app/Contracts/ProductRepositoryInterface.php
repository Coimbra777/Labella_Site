<?php

namespace App\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    /**
     * Listagem paginada da vitrine (somente ativos).
     *
     * @param  array<string, mixed>  $input  category_id?, require_featured?, search?, sort_by?, sort_order?, per_page?
     */
    public function paginatePublicCatalog(array $input): LengthAwarePaginator;

    public function findActiveByIdForPublic(int|string $id): Product;

    /**
     * Listagem paginada no painel (todos os estados).
     *
     * @param  array<string, mixed>  $input  search?, category_id?, is_active?, sort_by?, sort_order?, per_page?
     */
    public function paginateAdminList(array $input): LengthAwarePaginator;

    public function findWithCategoryForAdmin(int|string $id): Product;
}
