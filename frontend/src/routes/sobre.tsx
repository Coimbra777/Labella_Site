import { createFileRoute, Link } from "@tanstack/react-router";
import { SectionTitle } from "@/components/ui/States";
import { PillButton } from "@/components/ui/PillButton";
import { useSettings } from "@/hooks/useSettings";

export const Route = createFileRoute("/sobre")({
  head: () => ({
    meta: [
      { title: "Sobre — LaBella" },
      { name: "description", content: "Conheça a LaBella, marca de moda feminina." },
    ],
  }),
  component: AboutPage,
});

function AboutPage() {
  const { data: settings } = useSettings();
  const about =
    (typeof settings?.about_text === "string" && settings.about_text) ||
    "Na LaBella, acreditamos que cada mulher merece se sentir incrível todos os dias. Selecionamos peças com cuidado para combinar conforto, elegância e modernidade.";

  return (
    <section className="container mx-auto px-4 py-12">
      <SectionTitle title="Sobre a LaBella" />

      <div className="mt-10 grid md:grid-cols-2 gap-8 items-center">
        <div className="aspect-square rounded-3xl bg-gradient-to-br from-primary/20 to-white flex items-center justify-center">
          <span className="text-6xl md:text-7xl font-bold text-primary/80">LaBella</span>
        </div>
        <div>
          <h3 className="text-xl font-semibold text-foreground">Nossa história</h3>
          <p className="mt-3 text-muted-foreground leading-relaxed whitespace-pre-line">
            {about}
          </p>
          <div className="mt-6">
            <Link to="/produtos">
              <PillButton>Ver produtos</PillButton>
            </Link>
          </div>
        </div>
      </div>
    </section>
  );
}