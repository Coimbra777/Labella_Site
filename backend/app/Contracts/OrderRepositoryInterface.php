<?php

namespace App\Contracts;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $input  status?, payment_status?, search?, sort_by?, sort_order?, per_page?
     */
    public function paginateAdminList(array $input): LengthAwarePaginator;

    public function findWithAdminRelations(int|string $id): Order;
}
