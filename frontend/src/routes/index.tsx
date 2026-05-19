import { useState, useMemo } from "react";
import { createFileRoute, Link } from "@tanstack/react-router";
import { HeroBanner } from "@/components/HeroBanner";
import { ProductGrid } from "@/components/products/ProductGrid";
import { CategoryFilter } from "@/components/products/CategoryFilter";
import { EmptyState, ErrorState, LoadingSkeleton, SectionTitle } from "@/components/ui/States";
import { PillButton } from "@/components/ui/PillButton";
import { useProducts } from "@/hooks/useProducts";
import { useCategories } from "@/hooks/useCategories";

export const Route = createFileRoute("/")({
  head: () => ({
    meta: [
      { title: "LaBella — Moda feminina delicada e moderna" },
      {
        name: "description",
        content:
          "Vitrine de moda feminina LaBella. Monte sua seleção e solicite seu pedido com a nossa equipe.",
      },
    ],
  }),
  component: HomePage,
});

function HomePage() {
  const [categoryId, setCategoryId] = useState<number | null>(null);
  const { data: cats } = useCategories();
  const { data, isLoading, isError, refetch } = useProducts({
    per_page: 8,
    category_id: categoryId ?? undefined,
  });

  const products = useMemo(() => data?.data ?? [], [data]);

  return (
    <>
      <HeroBanner />

      <section className="container mx-auto px-4 py-12">
        <SectionTitle
          title="Produtos em destaque"
          subtitle="Peças selecionadas para você se sentir incrível."
        />

        {cats && cats.length > 0 && (
          <div className="mt-6">
            <CategoryFilter categories={cats} selectedId={categoryId} onSelect={setCategoryId} />
          </div>
        )}

        <div className="mt-8">
          {isLoading && <LoadingSkeleton count={8} />}
          {isError && <ErrorState onRetry={() => refetch()} />}
          {!isLoading && !isError && products.length === 0 && (
            <EmptyState
              title="Nenhum produto disponível"
              description="Em breve novidades por aqui."
            />
          )}
          {!isLoading && !isError && products.length > 0 && <ProductGrid products={products} />}
        </div>

        <div className="mt-10 flex justify-center">
          <Link to="/produtos">
            <PillButton variant="outline" size="lg">
              Ver todos os produtos
            </PillButton>
          </Link>
        </div>
      </section>

      <section className="container mx-auto px-4 pb-16">
        <div className="rounded-3xl bg-white p-8 md:p-12 text-center">
          <h3 className="text-2xl md:text-3xl font-semibold text-foreground">
            Pronta para escolher suas peças?
          </h3>
          <p className="mt-2 text-muted-foreground max-w-xl mx-auto">
            Monte sua seleção e envie sua solicitação. Nossa equipe entra em contato para confirmar
            disponibilidade, entrega e pagamento.
          </p>
          <div className="mt-6">
            <Link to="/produtos">
              <PillButton size="lg">Ver produtos</PillButton>
            </Link>
          </div>
        </div>
      </section>
    </>
  );
}
