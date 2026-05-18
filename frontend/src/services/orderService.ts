import { api } from "./api";
import type { CreateOrderApiBody, OrderCreateResult, OrderRequestPayload } from "@/types";

/**
 * Cria solicitação de pedido/orçamento. Apenas campos permitidos pela API.
 */
export const orderService = {
  async create(payload: OrderRequestPayload): Promise<OrderCreateResult> {
    const safe: OrderRequestPayload = {
      customer_name: payload.customer_name,
      customer_phone: payload.customer_phone,
      shipping_city: payload.shipping_city,
      items: payload.items.map((i) => ({
        product_id: i.product_id,
        quantity: i.quantity,
        size: i.size ?? null,
        color: i.color ?? null,
      })),
    };
    if (payload.customer_email) safe.customer_email = payload.customer_email;
    if (payload.shipping_address) safe.shipping_address = payload.shipping_address;
    if (payload.notes) safe.notes = payload.notes;

    const res = await api.post<CreateOrderApiBody>("/api/v1/orders", safe);

    const order = res?.order;
    const idRaw = order?.id;
    let id: number | undefined;
    if (typeof idRaw === "number" && Number.isFinite(idRaw)) {
      id = idRaw;
    } else if (typeof idRaw === "string" && /^\d+$/.test(idRaw)) {
      id = Number(idRaw);
    }

    return {
      message: res?.message,
      order_number: typeof order?.order_number === "string" ? order.order_number : undefined,
      id,
    };
  },
};
