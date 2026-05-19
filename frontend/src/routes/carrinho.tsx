import { createFileRoute, Link } from "@tanstack/react-router";
import { CartItemRow } from "@/components/cart/CartItemRow";
import { CartSummary } from "@/components/cart/CartSummary";
import { OrderRequestForm } from "@/components/order/OrderRequestForm";
import { EmptyState, SectionTitle } from "@/components/ui/States";
import { PillButton } from "@/components/ui/PillButton";
import { useCart } from "@/context/CartContext";

export const Route = createFileRoute("/carrinho")({
  head: () => ({
    meta: [
      { title: "Sua solicitação — LaBella" },
      { name: "description", content: "Revise suas peças e envie sua solicitação à LaBella." },
    ],
  }),
  component: CartPage,
});

function CartPage() {
  const { items } = useCart();

  return (
    <section className="container mx-auto px-4 py-12">
      <SectionTitle
        title="Sua solicitação"
        subtitle="Revise os itens e envie sua solicitação. Nossa equipe entra em contato com você."
      />

      {items.length === 0 ? (
        <div className="mt-8 max-w-lg mx-auto">
          <EmptyState
            title="Seu carrinho está vazio"
            description="Que tal dar uma olhada nas novidades?"
            action={
              <Link to="/produtos">
                <PillButton>Ver produtos</PillButton>
              </Link>
            }
          />
        </div>
      ) : (
        <div className="mt-8 grid lg:grid-cols-[1fr_380px] gap-8 items-start">
          <div className="space-y-3">
            {items.map((item) => (
              <CartItemRow
                key={`${item.product_id}-${item.size ?? ""}-${item.color ?? ""}`}
                item={item}
              />
            ))}
          </div>

          <div className="space-y-6 lg:sticky lg:top-20">
            <CartSummary />
            <OrderRequestForm />
          </div>
        </div>
      )}
    </section>
  );
}
