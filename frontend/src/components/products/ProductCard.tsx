import { Link } from "@tanstack/react-router";
import type { Product } from "@/types";
import { formatCurrency } from "@/utils/formatCurrency";
import { getProductImage, FALLBACK_IMAGE } from "@/utils/productImage";

export function ProductCard({ product }: { product: Product }) {
  const image = getProductImage(product);
  return (
    <Link
      to="/produtos/$id"
      params={{ id: String(product.id) }}
      className="group block rounded-2xl bg-white p-3 transition-all hover:-translate-y-0.5 hover:shadow-lg"
    >
      <div className="aspect-square w-full overflow-hidden rounded-xl bg-secondary">
        <img
          src={image}
          alt={product.name}
          loading="lazy"
          onError={(e) => {
            (e.currentTarget as HTMLImageElement).src = FALLBACK_IMAGE;
          }}
          className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
        />
      </div>
      <div className="mt-3 px-1">
        <h3 className="text-sm font-medium text-foreground line-clamp-2 min-h-10">
          {product.name}
        </h3>
        <p className="mt-1 text-primary font-semibold">{formatCurrency(product.price)}</p>
      </div>
    </Link>
  );
}
