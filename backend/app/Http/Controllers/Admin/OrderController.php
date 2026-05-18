<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    private const ALLOWED_SORT_COLUMNS = [
        'created_at',
        'customer_name',
        'order_number',
        'payment_status',
        'status',
        'total',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['user', 'items.product']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by order number or customer
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->string('sort_by')->toString();
        $sortBy = in_array($sortBy, self::ALLOWED_SORT_COLUMNS, true) ? $sortBy : 'created_at';

        $sortOrder = strtolower($request->string('sort_order', 'desc')->toString());
        $sortOrder = in_array($sortOrder, ['asc', 'desc'], true) ? $sortOrder : 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = max(1, min((int) $request->integer('per_page', 15), 100));
        $orders = $query->paginate($perPage);

        return response()->json($orders);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, OrderStatusService $orderStatusService): JsonResponse
    {
        $order = Order::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'sometimes|in:pending,paid,failed,refunded',
            'shipping_cost' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        try {
            $order = $orderStatusService->update($order, $data);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Verifique os dados do pedido.',
                'errors' => $e->errors(),
            ], 422);
        }

        return response()->json([
            'message' => 'Pedido atualizado com sucesso.',
            'order' => $order->load(['user', 'items.product'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $order = Order::findOrFail($id);
        
        // Only allow deletion of pending or cancelled orders
        if (!in_array($order->status, ['pending', 'cancelled'])) {
            return response()->json([
                'message' => 'Somente solicitações pendentes ou canceladas podem ser excluídas.',
            ], 422);
        }

        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully'
        ]);
    }
}
