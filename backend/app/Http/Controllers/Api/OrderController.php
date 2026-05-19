<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePublicOrderRequest;
use App\Http\Resources\OrderPublicResource;
use App\Jobs\SendNewOrderNotifications;
use App\Services\OrderCreationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Store a newly created order.
     */
    public function store(StorePublicOrderRequest $request, OrderCreationService $orderCreation): JsonResponse
    {
        try {
            $order = $orderCreation->createPendingPublicOrder($request->validated());

            try {
                SendNewOrderNotifications::dispatch($order->id)->afterCommit();
            } catch (\Throwable $e) {
                report($e);
            }

            return response()->json([
                'message' => 'Solicitação enviada com sucesso.',
                'order' => (new OrderPublicResource($order))->resolve(),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Verifique os dados do pedido.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Não foi possível criar a solicitação.',
            ], 500);
        }
    }
}
