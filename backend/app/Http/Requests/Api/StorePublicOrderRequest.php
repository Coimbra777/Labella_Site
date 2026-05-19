<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Solicitação pública de pedido (POST /api/v1/orders).
 */
class StorePublicOrderRequest extends FormRequest
{
    public const MAX_ITEMS = 20;

    public const MAX_ITEM_QUANTITY = 20;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $maxItems = self::MAX_ITEMS;

        return [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'nullable|string',
            'shipping_city' => 'required|string|max:255',
            'shipping_state' => 'nullable|string|max:255',
            'shipping_zip' => 'nullable|string|max:20',
            'shipping_country' => 'nullable|string|max:2',
            'items' => 'required|array|min:1|max:'.$maxItems,
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1|max:'.self::MAX_ITEM_QUANTITY,
            'items.*.size' => 'nullable|string',
            'items.*.color' => 'nullable|string',
            'items.*.price' => 'prohibited',
            'items.*.subtotal' => 'prohibited',
            'user_id' => 'prohibited',
            'order_number' => 'prohibited',
            'status' => 'prohibited',
            'payment_status' => 'prohibited',
            'shipping_cost' => 'prohibited',
            'discount' => 'prohibited',
            'total' => 'prohibited',
            'subtotal' => 'prohibited',
            'payment_method' => 'prohibited',
            'created_at' => 'prohibited',
            'updated_at' => 'prohibited',
            'deleted_at' => 'prohibited',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $maxItems = self::MAX_ITEMS;
        $maxQty = self::MAX_ITEM_QUANTITY;

        return [
            'customer_name.required' => 'O nome completo é obrigatório.',
            'customer_phone.required' => 'O telefone é obrigatório.',
            'customer_email.email' => 'Informe um e-mail válido.',
            'shipping_city.required' => 'A cidade é obrigatória.',
            'items.required' => 'O carrinho está vazio. Adicione produtos antes de finalizar.',
            'items.min' => 'O carrinho está vazio. Adicione produtos antes de finalizar.',
            'items.max' => 'Limite máximo de '.$maxItems.' itens por solicitação.',
            'items.*.product_id.required' => 'Produto inválido no carrinho.',
            'items.*.product_id.exists' => 'Um ou mais produtos não foram encontrados.',
            'items.*.quantity.required' => 'Informe a quantidade do produto.',
            'items.*.quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'items.*.quantity.min' => 'A quantidade mínima é 1.',
            'items.*.quantity.max' => 'A quantidade máxima por item é '.$maxQty.'.',
            'items.*.price.prohibited' => 'O preço dos itens é calculado pelo servidor.',
            'items.*.subtotal.prohibited' => 'O subtotal dos itens é calculado pelo servidor.',
            'user_id.prohibited' => 'Campos administrativos não podem ser enviados na solicitação.',
            'order_number.prohibited' => 'Campos administrativos não podem ser enviados na solicitação.',
            'status.prohibited' => 'Campos administrativos não podem ser enviados na solicitação.',
            'payment_status.prohibited' => 'Campos administrativos não podem ser enviados na solicitação.',
            'shipping_cost.prohibited' => 'Campos financeiros são definidos pelo administrador.',
            'discount.prohibited' => 'Campos financeiros são definidos pelo administrador.',
            'total.prohibited' => 'Campos financeiros são definidos pelo administrador.',
            'subtotal.prohibited' => 'Campos financeiros são definidos pelo administrador.',
            'payment_method.prohibited' => 'A forma de pagamento é definida posteriormente pelo administrador.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Verifique os dados do formulário.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
