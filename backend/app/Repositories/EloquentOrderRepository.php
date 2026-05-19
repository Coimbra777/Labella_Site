<?php

namespace App\Repositories;

use App\Contracts\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    private const ALLOWED_SORT_COLUMNS = [
        'created_at',
        'customer_name',
        'order_number',
        'payment_status',
        'status',
        'total',
    ];

    public function paginateAdminList(array $input): LengthAwarePaginator
    {
        $query = Order::with(['user', 'items.product']);

        if (! empty($input['status'])) {
            $query->where('status', $input['status']);
        }

        if (! empty($input['payment_status'])) {
            $query->where('payment_status', $input['payment_status']);
        }

        if (! empty($input['search'])) {
            $search = (string) $input['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $sortBy = (string) ($input['sort_by'] ?? '');
        $sortBy = in_array($sortBy, self::ALLOWED_SORT_COLUMNS, true) ? $sortBy : 'created_at';

        $sortOrder = strtolower((string) ($input['sort_order'] ?? 'desc'));
        $sortOrder = in_array($sortOrder, ['asc', 'desc'], true) ? $sortOrder : 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $perPage = (int) ($input['per_page'] ?? 15);
        $perPage = max(1, min($perPage, 100));

        return $query->paginate($perPage);
    }

    public function findWithAdminRelations(int|string $id): Order
    {
        return Order::with(['user', 'items.product'])->findOrFail($id);
    }
}
