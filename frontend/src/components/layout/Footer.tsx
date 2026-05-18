import { Link } from "@tanstack/react-router";
import { Instagram, Facebook, Mail, Phone, MessageCircle, MapPin } from "lucide-react";
import { useSettings } from "@/hooks/useSettings";

function waLink(num?: string) {
  if (!num) return null;
  const clean = num.replace(/\D/g, "");
  if (!clean) return null;
  return `https://wa.me/${clean}`;
}

export function Footer() {
  const { data: s } = useSettings();
  const instagramHref = s?.instagramUrl?.trim() || undefined;

  return (
    <footer className="bg-primary text-primary-foreground mt-16">
      <div className="container mx-auto px-4 py-12 grid gap-10 md:grid-cols-4">
        <div>
          <h3 className="text-xl font-semibold">LaBella</h3>
          <p className="mt-3 text-sm opacity-90 leading-relaxed">
            Moda feminina delicada e moderna. Selecionamos peças com carinho para o seu dia a dia.
          </p>
        </div>

        <div>
          <h4 className="text-sm font-semibold uppercase tracking-wide opacity-90">Navegação</h4>
          <ul className="mt-3 space-y-2 text-sm">
            <li>
              <Link to="/" className="hover:underline opacity-90">
                Início
              </Link>
            </li>
            <li>
              <Link to="/produtos" className="hover:underline opacity-90">
                Produtos
              </Link>
            </li>
            <li>
              <Link to="/sobre" className="hover:underline opacity-90">
                Sobre
              </Link>
            </li>
            <li>
              <Link to="/contato" className="hover:underline opacity-90">
                Contato
              </Link>
            </li>
          </ul>
        </div>

        <div>
          <h4 className="text-sm font-semibold uppercase tracking-wide opacity-90">Contato</h4>
          <ul className="mt-3 space-y-2 text-sm">
            {s?.phone && (
              <li className="flex items-center gap-2">
                <Phone size={14} /> <span>{s.phone}</span>
              </li>
            )}
            {s?.whatsapp && waLink(s.whatsapp) && (
              <li>
                <a
                  href={waLink(s.whatsapp)!}
                  target="_blank"
                  rel="noreferrer"
                  className="inline-flex items-center gap-2 hover:underline"
                >
                  <MessageCircle size={14} /> WhatsApp
                </a>
              </li>
            )}
            {s?.email && (
              <li className="flex items-center gap-2">
                <Mail size={14} /> <span>{s.email}</span>
              </li>
            )}
            {s?.address && (
              <li className="flex items-center gap-2">
                <MapPin size={14} />
                <span>{s.address}</span>
              </li>
            )}
          </ul>
        </div>

        <div>
          <h4 className="text-sm font-semibold uppercase tracking-wide opacity-90">
            Redes sociais
          </h4>
          <div className="mt-3 flex items-center gap-2">
            {instagramHref && (
              <a
                href={instagramHref}
                target="_blank"
                rel="noreferrer"
                aria-label="Instagram"
                className="h-10 w-10 rounded-full bg-white/15 hover:bg-white/25 transition inline-flex items-center justify-center"
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
                className="h-10 w-10 rounded-full bg-white/15 hover:bg-white/25 transition inline-flex items-center justify-center"
              >
                <Facebook size={18} />
              </a>
            )}
          </div>
        </div>
      </div>
      <div className="border-t border-white/15">
        <div className="container mx-auto px-4 py-4 text-xs opacity-80 text-center">
          © {new Date().getFullYear()} LaBella. Todos os direitos reservados.
        </div>
      </div>
    </footer>
  );
}
