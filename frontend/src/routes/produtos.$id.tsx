import { useState } from "react";
import { createFileRoute, Link } from "@tanstack/react-router";
import { Check } from "lucide-react";
import { ProductGallery } from "@/components/products/ProductGallery";
import { PillButton } from "@/components/ui/PillButton";
import { ErrorState, LoadingSkeleton } from "@/components/ui/States";
import { useProduct } from "@/hooks/useProducts";
import { formatCurrency } from "@/utils/formatCurrency";
import { getProductImage, getProductImages } from "@/utils/productImage";
import { useCart } from "@/context/CartContext";
import { cn } from "@/lib/utils";
import { MAX_QUANTITY_PER_LINE } from "@/constants/cart";
import { normalizeColors, normalizeSizes } from "@/utils/productVariants";

export const Route = createFileRoute("/produtos/$id")({
  component: ProductDetailPage,
});

function ProductDetailPage() {
  const { id } = Route.useParams();
  const { data: product, isLoading, isError, refetch } = useProduct(id);
  const { addItem } = useCart();

  const [quantity, setQuantity] = useState(1);
  const [size, setSize] = useState<string | null>(null);
  const [color, setColor] = useState<string | null>(null);
  const [added, setAdded] = useState(false);

  if (isLoading) {
    return (
      <section className="container mx-auto px-4 py-12">
        <LoadingSkeleton count={2} />
      </section>
    );
  }
  if (isError || !product) {
    return (
      <section className="container mx-auto px-4 py-12">
        <ErrorState
          title="Produto não encontrado"
          description="Verifique o link ou volte aos produtos."
          onRetry={() => refetch()}
        />
        <div className="mt-6 text-center">
          <Link to="/produtos">
            <PillButton variant="outline">Ver produtos</PillButton>
          </Link>
        </div>
      </section>
    );
  }

  const sizes = normalizeSizes(product.sizes);
  const colors = normalizeColors(product.colors);
  const images = getProductImages(product);

  function handleAdd() {
    if (sizes.length > 0 && !size) return;
    if (colors.length > 0 && !color) return;
    if (!product) return;
    addItem({
      product_id: product.id,
      name: product.name,
      price: Number(product.price) || 0,
      image: getProductImage(product),
      quantity,
      size,
      color,
    });
    setAdded(true);
    window.setTimeout(() => setAdded(false), 2000);
  }

  return (
    <section className="container mx-auto px-4 py-10 md:py-16">
      <div className="grid md:grid-cols-2 gap-8 lg:gap-12">
        <ProductGallery images={images} alt={product.name} />

        <div className="flex flex-col">
          {product.category?.name && (
            <span className="text-xs uppercase tracking-widest text-primary font-semibold">
              {product.category.name}
            </span>
          )}
          <h1 className="mt-1 text-2xl md:text-3xl font-semibold text-foreground">
            {product.name}
          </h1>
          <p className="mt-2 text-2xl text-primary font-semibold">
            {formatCurrency(product.price)}
          </p>
          <p className="text-xs text-muted-foreground">Preço de referência</p>

          {product.description && (
            <p className="mt-5 text-sm text-muted-foreground leading-relaxed whitespace-pre-line">
              {product.description}
            </p>
          )}

          {sizes.length > 0 && (
            <div className="mt-6">
              <p className="text-sm font-medium mb-2">Tamanho</p>
              <div className="flex flex-wrap gap-2">
                {sizes.map((s) => (
                  <button
                    key={s}
                    type="button"
                    onClick={() => setSize(s)}
                    className={cn(
                      "h-10 min-w-10 px-3 rounded-full border text-sm font-medium",
                      size === s
                        ? "bg-primary text-primary-foreground border-primary"
                        : "bg-white border-border hover:border-primary",
                    )}
                  >
                    {s}
                  </button>
                ))}
              </div>
            </div>
          )}

          {colors.length > 0 && (
            <div className="mt-4">
              <p className="text-sm font-medium mb-2">Cor</p>
              <div className="flex flex-wrap gap-2">
                {colors.map((c) => (
                  <button
                    key={c}
                    type="button"
                    onClick={() => setColor(c)}
                    className={cn(
                      "h-10 px-4 rounded-full border text-sm font-medium",
                      color === c
                        ? "bg-primary text-primary-foreground border-primary"
                        : "bg-white border-border hover:border-primary",
                    )}
                  >
                    {c}
                  </button>
                ))}
              </div>
            </div>
          )}

          <div className="mt-6">
            <p className="text-sm font-medium mb-2">Quantidade</p>
            <div className="inline-flex items-center rounded-full border border-border bg-white">
              <button
                type="button"
                onClick={() => setQuantity((q) => Math.max(1, q - 1))}
                className="h-11 w-11 text-lg hover:text-primary"
              >
                −
              </button>
              <span className="w-10 text-center">{quantity}</span>
              <button
                type="button"
                onClick={() => setQuantity((q) => Math.min(MAX_QUANTITY_PER_LINE, q + 1))}
                disabled={quantity >= MAX_QUANTITY_PER_LINE}
                className="h-11 w-11 text-lg hover:text-primary disabled:opacity-40"
              >
                +
              </button>
            </div>
          </div>

          <div className="mt-8">
            <PillButton size="lg" className="w-full sm:w-auto" onClick={handleAdd}>
              {added ? (
                <>
                  <Check size={18} /> Adicionado ao carrinho
                </>
              ) : (
                "Adicionar ao carrinho"
              )}
            </PillButton>
          </div>

          {((sizes.length > 0 && !size) || (colors.length > 0 && !color)) && (
            <p className="mt-2 text-xs text-muted-foreground">
              Selecione {sizes.length > 0 && !size ? "o tamanho" : ""}
              {sizes.length > 0 && !size && colors.length > 0 && !color ? " e " : ""}
              {colors.length > 0 && !color ? "a cor" : ""} para adicionar.
            </p>
          )}
        </div>
      </div>
    </section>
  );
}
