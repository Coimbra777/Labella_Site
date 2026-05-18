import { useState } from "react";
import { Link, useRouterState } from "@tanstack/react-router";
import { Menu, ShoppingBag, X } from "lucide-react";
import { useCart } from "@/context/CartContext";
import { useSettings } from "@/hooks/useSettings";
import { cn } from "@/lib/utils";
import { Logo } from "@/components/Logo";

const NAV = [
  { to: "/", label: "Início" },
  { to: "/produtos", label: "Produtos" },
  { to: "/sobre", label: "Sobre" },
  { to: "/contato", label: "Contato" },
] as const;

function waHref(digits?: string) {
  if (!digits?.trim()) return null;
  return `https://wa.me/${digits.replace(/\D/g, "")}`;
}

export function Header() {
  const [open, setOpen] = useState(false);
  const { totalItems } = useCart();
  const { data: settings } = useSettings();
  const pathname = useRouterState({ select: (s) => s.location.pathname });

  const wa = settings?.whatsapp;
  const linkWa = waHref(wa);

  return (
    <header className="sticky top-0 z-40 border-b border-border bg-background/85 backdrop-blur">
      {settings?.phone && (
        <div className="hidden sm:block bg-primary text-primary-foreground text-center text-xs py-1.5 px-4">
          <span className="opacity-95">Fale conosco: </span>
          <span className="font-medium">{settings.phone}</span>
          {linkWa && (
            <>
              {" · "}
              <a
                href={linkWa}
                className="underline underline-offset-2 font-medium"
                target="_blank"
                rel="noreferrer"
              >
                WhatsApp
              </a>
            </>
          )}
        </div>
      )}
      <div className="container mx-auto px-4 h-16 flex items-center justify-between gap-4">
        <Link to="/" className="flex items-center gap-2">
          <Logo className="text-primary" />
        </Link>

        <nav className="hidden md:flex items-center gap-1">
          {NAV.map((n) => (
            <Link
              key={n.to}
              to={n.to}
              className={cn(
                "px-4 h-10 inline-flex items-center rounded-full text-sm font-medium transition",
                pathname === n.to
                  ? "bg-primary text-primary-foreground"
                  : "text-foreground hover:text-primary",
              )}
            >
              {n.label}
            </Link>
          ))}
        </nav>

        <div className="flex items-center gap-2">
          <Link
            to="/carrinho"
            aria-label="Sua solicitação"
            className="relative h-11 w-11 inline-flex items-center justify-center rounded-full bg-white hover:bg-secondary transition"
          >
            <ShoppingBag size={20} className="text-primary" />
            {totalItems > 0 && (
              <span className="absolute -top-1 -right-1 bg-primary text-primary-foreground text-[10px] h-5 min-w-5 px-1 rounded-full inline-flex items-center justify-center font-semibold">
                {totalItems}
              </span>
            )}
          </Link>
          <button
            type="button"
            aria-label="Menu"
            onClick={() => setOpen((o) => !o)}
            className="md:hidden h-11 w-11 inline-flex items-center justify-center rounded-full bg-white hover:bg-secondary transition"
          >
            {open ? <X size={20} /> : <Menu size={20} />}
          </button>
        </div>
      </div>

      {open && (
        <div className="md:hidden border-t border-border bg-background">
          <div className="container mx-auto px-4 py-3 flex flex-col gap-1">
            {NAV.map((n) => (
              <Link
                key={n.to}
                to={n.to}
                onClick={() => setOpen(false)}
                className={cn(
                  "px-4 h-11 inline-flex items-center rounded-full text-sm font-medium",
                  pathname === n.to
                    ? "bg-primary text-primary-foreground"
                    : "text-foreground hover:bg-secondary",
                )}
              >
                {n.label}
              </Link>
            ))}
          </div>
        </div>
      )}
    </header>
  );
}
