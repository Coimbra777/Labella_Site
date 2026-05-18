import { useMutation } from "@tanstack/react-query";
import { orderService } from "@/services/orderService";
import type { OrderRequestPayload } from "@/types";

export function useOrderRequest() {
  return useMutation({
    mutationFn: (payload: OrderRequestPayload) => orderService.create(payload),
  });
}