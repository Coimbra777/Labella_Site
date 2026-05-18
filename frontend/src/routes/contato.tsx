import { createFileRoute } from "@tanstack/react-router";
import { Mail, MapPin, MessageCircle, Phone, Instagram, Facebook } from "lucide-react";
import { SectionTitle } from "@/components/ui/States";
import { useSettings } from "@/hooks/useSettings";

export const Route = createFileRoute("/contato")({
  head: () => ({
    meta: [
      { title: "Contato — LaBella" },
      { name: "description", content: "Fale com a equipe LaBella." },
    ],
  }),
  component: ContactPage,
});

function ContactPage() {
  const { data: s } = useSettings();
  const wa = s?.whatsapp?.replace(/\D/g, "");
  const instagramHref = s?.instagramUrl?.trim() || undefined;

  const items: { icon: React.ReactNode; label: string; value: React.ReactNode }[] = [];
  if (s?.phone) items.push({ icon: <Phone size={18} />, label: "Telefone", value: s.phone });
  if (wa)
    items.push({
      icon: <MessageCircle size={18} />,
      label: "WhatsApp",
      value: (
        <a
          className="text-primary hover:underline"
          href={`https://wa.me/${wa}`}
          target="_blank"
          rel="noreferrer"
        >
          Enviar mensagem
        </a>
      ),
    });
  if (s?.email)
    items.push({
      icon: <Mail size={18} />,
      label: "E-mail",
      value: (
        <a className="text-primary hover:underline" href={`mailto:${s.email}`}>
          {s.email}
        </a>
      ),
    });
  if (s?.address)
    items.push({
      icon: <MapPin size={18} />,
      label: "Endereço",
      value: s.address,
    });

  return (
    <section className="container mx-auto px-4 py-12">
      <SectionTitle
        title="Fale com a gente"
        subtitle="Estamos aqui para ajudar com qualquer dúvida sobre nossas peças."
      />

      <div className="mt-10 grid md:grid-cols-2 gap-6 max-w-3xl mx-auto">
        {items.length === 0 ? (
          <p className="md:col-span-2 text-center text-muted-foreground bg-white rounded-2xl p-6">
            Em breve nossas informações de contato estarão disponíveis aqui.
          </p>
        ) : (
          items.map((it) => (
            <div key={it.label} className="bg-white rounded-2xl p-5 flex gap-3 items-start">
              <div className="h-10 w-10 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0">
                {it.icon}
              </div>
              <div className="min-w-0">
                <p className="text-xs uppercase tracking-wide text-muted-foreground">{it.label}</p>
                <p className="text-foreground break-words">{it.value}</p>
              </div>
            </div>
          ))
        )}
      </div>

      {(instagramHref || s?.facebook) && (
        <div className="mt-8 flex items-center justify-center gap-3">
          {instagramHref && (
            <a
              href={instagramHref}
              target="_blank"
              rel="noreferrer"
              aria-label="Instagram"
              className="h-11 w-11 rounded-full bg-primary text-primary-foreground hover:opacity-90 inline-flex items-center justify-center"
            >
              <Instagram size={18} />
            </a>
          )}
          {s?.facebook && (
            <a
              href={s.facebook}
              target="_blank"
              rel="noreferrer"
              aria-label="Facebook"
              className="h-11 w-11 rounded-full bg-primary text-primary-foreground hover:opacity-90 inline-flex items-center justify-center"
            >
              <Facebook size={18} />
            </a>
          )}
        </div>
      )}
    </section>
  );
}
