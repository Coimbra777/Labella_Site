import { api } from "./api";
import type { Paginated, Product } from "@/types";

/** Só desembrulha `{ data: Product }` se `data` for objeto de produto (não array). */
function unwrapProductResponse(res: unknown): Product {
  if (
    res &&
    typeof res === "object" &&
    "data" in res &&
    (res as { data: unknown }).data !== null &&
    typeof (res as { data: unknown }).data === "object" &&
    !Array.isArray((res as { data: unknown }).data) &&
    "id" in ((res as { data: object }).data as object)
  ) {
    return (res as { data: Product }).data;
  }
  return res as Product;
}

export const productService = {
  async list(params: { page?: number; per_page?: number; category_id?: number; search?: string } = {}) {
    const q = new URLSearchParams();
    if (params.page) q.set("page", String(params.page));
    if (params.per_page) q.set("per_page", String(params.per_page));
    if (params.category_id) q.set("category_id", String(params.category_id));
    if (params.search) q.set("search", params.search);
    const qs = q.toString();
    const res = await api.get<Paginated<Product> | { data: Paginated<Product> }>(
      `/api/v1/products${qs ? `?${qs}` : ""}`,
    );
    // Handle either {data:[...]} or {data:{data:[...]}}
    const maybeNested = res as { data?: unknown };
    if (
      maybeNested.data &&
      typeof maybeNested.data === "object" &&
      "data" in (maybeNested.data as object)
    ) {
      return maybeNested.data as Paginated<Product>;
    }
    return res as Paginated<Product>;
  },
  async getById(id: number | string) {
    const res = await api.get<Product | { data: Product }>(`/api/v1/products/${id}`);
    return unwrapProductResponse(res);
  },
};