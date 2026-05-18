import { useCart } from "@/context/CartContext";
import { formatCurrency } from "@/utils/formatCurrency";

export function CartSummary() {
  const { subtotal, totalItems } = useCart();
  return (
    <div className="bg-white rounded-2xl p-4 space-y-2">
      <div className="flex items-center justify-between text-sm">
        <span className="text-muted-foreground">Itens</span>
        <span className="text-foreground font-medium">{totalItems}</span>
      </div>
      <div className="flex items-center justify-between">
        <span className="text-muted-foreground text-sm">Subtotal (referência)</span>
        <span className="text-primary text-lg font-semibold">
          {formatCurrency(subtotal)}
        </span>
      </div>
      <p className="text-xs text-muted-foreground leading-relaxed pt-2 border-t border-border">
        Os valores oficiais (frete, descontos e total final) serão confirmados
        pela nossa equipe.
      </p>
    </div>
  );
}