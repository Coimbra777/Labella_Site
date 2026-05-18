import { Minus, Plus, Trash2 } from "lucide-react";
import type { CartItem } from "@/types";
import { formatCurrency } from "@/utils/formatCurrency";
import { useCart } from "@/context/CartContext";
import { FALLBACK_IMAGE } from "@/utils/productImage";
import { MAX_QUANTITY_PER_LINE } from "@/constants/cart";

export function CartItemRow({ item }: { item: CartItem }) {
  const { updateQuantity, removeItem } = useCart();
  return (
    <div className="flex gap-3 sm:gap-4 bg-white rounded-2xl p-3">
      <div className="h-20 w-20 sm:h-24 sm:w-24 shrink-0 overflow-hidden rounded-xl bg-secondary">
        <img
          src={item.image || FALLBACK_IMAGE}
          alt={item.name}
          onError={(e) => {
            (e.currentTarget as HTMLImageElement).src = FALLBACK_IMAGE;
          }}
          className="h-full w-full object-cover"
        />
      </div>
      <div className="flex-1 min-w-0 flex flex-col justify-between">
        <div>
          <h4 className="text-sm font-medium text-foreground line-clamp-2">{item.name}</h4>
          <p className="text-xs text-muted-foreground mt-0.5">
            {item.size && (
              <>
                Tam: <span className="text-foreground">{item.size}</span>
              </>
            )}
            {item.size && item.color && " · "}
            {item.color && (
              <>
                Cor: <span className="text-foreground">{item.color}</span>
              </>
            )}
          </p>
        </div>
        <div className="flex items-center justify-between mt-2">
          <div className="inline-flex items-center rounded-full border border-border bg-background">
            <button
              type="button"
              aria-label="Diminuir"
              onClick={() =>
                updateQuantity(item.product_id, item.quantity - 1, item.size, item.color)
              }
              className="h-8 w-8 flex items-center justify-center text-foreground hover:text-primary"
            >
              <Minus size={14} />
            </button>
            <span className="w-8 text-center text-sm">{item.quantity}</span>
            <button
              type="button"
              aria-label="Aumentar"
              disabled={item.quantity >= MAX_QUANTITY_PER_LINE}
              onClick={() =>
                updateQuantity(item.product_id, item.quantity + 1, item.size, item.color)
              }
              className="h-8 w-8 flex items-center justify-center text-foreground hover:text-primary disabled:opacity-40"
            >
              <Plus size={14} />
            </button>
          </div>
          <p className="text-sm font-semibold text-primary">
            {formatCurrency(item.price * item.quantity)}
          </p>
        </div>
      </div>
      <button
        type="button"
        aria-label="Remover"
        onClick={() => removeItem(item.product_id, item.size, item.color)}
        className="self-start text-muted-foreground hover:text-destructive transition"
      >
        <Trash2 size={18} />
      </button>
    </div>
  );
}
