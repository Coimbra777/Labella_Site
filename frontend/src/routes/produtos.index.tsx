import { useEffect, useState } from "react";
import { createFileRoute } from "@tanstack/react-router";
import type { Product } from "@/types";
import { ProductGrid } from "@/components/products/ProductGrid";
import { CategoryFilter } from "@/components/products/CategoryFilter";
import { EmptyState, ErrorState, LoadingSkeleton, SectionTitle } from "@/components/ui/States";
import { Input } from "@/components/ui/TextField";
import { PillButton } from "@/components/ui/PillButton";
import { useProducts } from "@/hooks/useProducts";
import { useCategories } from "@/hooks/useCategories";

export const Route = createFileRoute("/produtos/")({
  head: () => ({
    meta: [
      { title: "Produtos — LaBella" },
      { name: "description", content: "Explore nossa coleção de moda feminina LaBella." },
    ],
  }),
  component: ProductsPage,
});

function ProductsPage() {
  const [categoryId, setCategoryId] = useState<number | null>(null);
  const [search, setSearch] = useState("");
  const [page, setPage] = useState(1);
  const [accumulated, setAccumulated] = useState<Product[]>([]);
  const perPage = 12;

  const { data: cats } = useCategories();
  const { data, isLoading, isError, refetch, isFetching } = useProducts({
    page,
    per_page: perPage,
    category_id: categoryId ?? undefined,
    search: search || undefined,
  });

  useEffect(() => {
    setPage(1);
    setAccumulated([]);
  }, [categoryId, search]);

  useEffect(() => {
    if (!data?.data) return;
    const chunk = data.data as Product[];
    setAccumulated((prev) => {
      if (page === 1) return chunk;
      const ids = new Set(prev.map((p) => p.id));
      return [...prev, ...chunk.filter((p) => !ids.has(p.id))];
    });
  }, [data, page]);

  const lastPage = data?.last_page ?? 1;
  const canLoadMore = page < lastPage;

  const showInitialLoading = isLoading && page === 1 && accumulated.length === 0;

  return (
    <section className="container mx-auto px-4 py-12">
      <SectionTitle title="Nossos produtos" />

      <div className="mt-6 max-w-md mx-auto">
        <Input
          placeholder="Buscar produto..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
        />
      </div>

      {cats && cats.length > 0 && (
        <div className="mt-6">
          <CategoryFilter categories={cats} selectedId={categoryId} onSelect={setCategoryId} />
        </div>
      )}

      <div className="mt-8">
        {showInitialLoading && <LoadingSkeleton count={8} />}
        {isError && accumulated.length === 0 && !isFetching && (
          <ErrorState onRetry={() => refetch()} />
        )}
        {!showInitialLoading && !isError && accumulated.length === 0 && !isFetching && (
          <EmptyState
            title="Nenhum produto encontrado"
            description="Tente ajustar o filtro ou a busca."
          />
        )}
        {accumulated.length > 0 && <ProductGrid products={accumulated} />}
      </div>

      {canLoadMore && (
        <div className="mt-10 flex justify-center">
          <PillButton
            variant="outline"
            size="lg"
            onClick={() => setPage((p) => p + 1)}
            disabled={isFetching}
          >
            {isFetching ? "Carregando..." : "Carregar mais"}
          </PillButton>
        </div>
      )}
    </section>
  );
}
