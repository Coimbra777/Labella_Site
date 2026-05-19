<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\OrderRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderRequest;
use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $input = [
            'search' => $request->has('search') ? (string) $request->search : null,
            'sort_by' => $request->string('sort_by')->toString(),
            'sort_order' => $request->string('sort_order', 'desc')->toString(),
            'per_page' => max(1, min((int) $request->integer('per_page', 15), 100)),
        ];

        if ($request->has('status')) {
            $input['status'] = $request->status;
        }

        if ($request->has('payment_status')) {
            $input['payment_status'] = $request->payment_status;
        }

        $orders = $this->orders->paginateAdminList($input);

        return response()->json($orders);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $order = $this->orders->findWithAdminRelations($id);

        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, string $id, OrderStatusService $orderStatusService): JsonResponse
    {
        $order = Order::findOrFail($id);

        $data = $request->validated();
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
            'order' => $order->load(['user', 'items.product']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $order = Order::findOrFail($id);

        // Only allow deletion of pending or cancelled orders
        if (! in_array($order->status, ['pending', 'cancelled'])) {
            return response()->json([
                'message' => 'Somente solicitações pendentes ou canceladas podem ser excluídas.',
            ], 422);
        }

        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully',
        ]);
    }
}
