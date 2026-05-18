import { useEffect, useState } from "react";
import { createFileRoute, Link } from "@tanstack/react-router";
import { CheckCircle2, MessageCircle } from "lucide-react";
import { PillButton } from "@/components/ui/PillButton";
import { useSettings } from "@/hooks/useSettings";
import { SESSION_LAST_ORDER_NUMBER_KEY } from "@/constants/cart";

interface Search {
  n?: string;
}

export const Route = createFileRoute("/confirmacao")({
  validateSearch: (search: Record<string, unknown>): Search => ({
    n: typeof search.n === "string" ? search.n : undefined,
  }),
  head: () => ({
    meta: [{ title: "Solicitação enviada — LaBella" }, { name: "robots", content: "noindex" }],
  }),
  component: ConfirmationPage,
});

function ConfirmationPage() {
  const { n: nFromUrl } = Route.useSearch();
  const { data: settings } = useSettings();
  const wa = settings?.whatsapp?.replace(/\D/g, "");

  const [displayNumber, setDisplayNumber] = useState<string | undefined>(() => nFromUrl);

  useEffect(() => {
    if (nFromUrl) {
      setDisplayNumber(nFromUrl);
      return;
    }
    try {
      const stored = sessionStorage.getItem(SESSION_LAST_ORDER_NUMBER_KEY);
      if (stored) {
        setDisplayNumber(stored);
        sessionStorage.removeItem(SESSION_LAST_ORDER_NUMBER_KEY);
      }
    } catch {
      /* ignore */
    }
  }, [nFromUrl]);

  return (
    <section className="container mx-auto px-4 py-16">
      <div className="max-w-xl mx-auto text-center bg-white rounded-3xl p-8 md:p-12">
        <div className="mx-auto h-16 w-16 rounded-full bg-primary/10 text-primary flex items-center justify-center">
          <CheckCircle2 size={36} />
        </div>
        <h1 className="mt-4 text-2xl md:text-3xl font-semibold text-foreground">
          Recebemos sua solicitação!
        </h1>
        <p className="mt-3 text-muted-foreground leading-relaxed">
          Nossa equipe entrará em contato para confirmar disponibilidade, entrega e forma de
          pagamento.
        </p>
        {displayNumber && (
          <p className="mt-4 inline-block bg-secondary px-4 py-2 rounded-full text-sm">
            Nº da solicitação: <span className="font-semibold text-primary">{displayNumber}</span>
          </p>
        )}

        <div className="mt-8 text-left bg-secondary rounded-2xl p-4 text-sm text-muted-foreground space-y-1">
          <p className="font-medium text-foreground">Próximos passos:</p>
          <p>• Nossa equipe verificará disponibilidade dos itens.</p>
          <p>• Entraremos em contato no telefone informado.</p>
          <p>• Confirmaremos valores, entrega e pagamento.</p>
        </div>

        <div className="mt-8 flex flex-wrap justify-center gap-3">
          <Link to="/produtos">
            <PillButton size="lg">Voltar aos produtos</PillButton>
          </Link>
          {wa && (
            <a href={`https://wa.me/${wa}`} target="_blank" rel="noreferrer">
              <PillButton variant="outline" size="lg">
                <MessageCircle size={18} /> WhatsApp
              </PillButton>
            </a>
          )}
        </div>
      </div>
    </section>
  );
}
