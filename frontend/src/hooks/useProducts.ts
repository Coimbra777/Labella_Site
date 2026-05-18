import { useQuery } from "@tanstack/react-query";
import { productService } from "@/services/productService";

interface Params {
  page?: number;
  per_page?: number;
  category_id?: number;
  search?: string;
}

export function useProducts(params: Params = {}) {
  return useQuery({
    queryKey: ["products", params],
    queryFn: () => productService.list(params),
  });
}

export function useProduct(id: number | string | undefined) {
  return useQuery({
    queryKey: ["product", id],
    queryFn: () => productService.getById(id as number),
    enabled: id !== undefined && id !== null && id !== "",
  });
}