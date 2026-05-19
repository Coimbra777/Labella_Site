import { api } from "./api";
import type { Category } from "@/types";

export const categoryService = {
  async list() {
    const res = await api.get<Category[] | { data: Category[] }>("/api/v1/categories");
    if (Array.isArray(res)) return res;
    return (res as { data: Category[] }).data ?? [];
  },
};
