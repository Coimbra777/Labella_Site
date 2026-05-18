import { Link } from "@tanstack/react-router";
import { PillButton } from "@/components/ui/PillButton";

export function HeroBanner() {
  return (
    <section className="relative overflow-hidden">
      <div
        aria-hidden
        className="absolute inset-0 -z-10 opacity-60"
        style={{
          background:
            "radial-gradient(60% 50% at 80% 10%, rgba(255,0,140,0.18), transparent 70%), radial-gradient(50% 40% at 10% 90%, rgba(255,0,140,0.12), transparent 70%)",
        }}
      />
      <div className="container mx-auto px-4 py-16 md:py-24 grid md:grid-cols-2 gap-10 items-center">
        <div>
          <span className="inline-block text-xs uppercase tracking-widest text-primary font-semibold bg-white px-3 py-1 rounded-full">
            Nova coleção
          </span>
          <h1 className="mt-4 text-4xl md:text-5xl lg:text-6xl font-semibold text-foreground leading-tight">
            Moda feminina <span className="text-primary">com delicadeza</span> e atitude.
          </h1>
          <p className="mt-4 text-muted-foreground text-base md:text-lg max-w-lg">
            Descubra peças cuidadosamente selecionadas para o seu estilo.
            Monte sua seleção e envie sua solicitação — nossa equipe entra em
            contato com você.
          </p>
          <div className="mt-6 flex flex-wrap gap-3">
            <Link to="/produtos">
              <PillButton size="lg">Ver produtos</PillButton>
            </Link>
            <Link to="/sobre">
              <PillButton variant="outline" size="lg">
                Sobre nós
              </PillButton>
            </Link>
          </div>
        </div>
        <div className="relative">
          <div className="aspect-square w-full max-w-md mx-auto rounded-[2rem] bg-gradient-to-br from-primary/20 to-white shadow-xl overflow-hidden flex items-center justify-center">
            <span className="text-7xl md:text-8xl font-bold text-primary/80">LaBella</span>
          </div>
        </div>
      </div>
    </section>
  );
}