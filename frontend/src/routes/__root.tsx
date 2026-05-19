import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import {
  Outlet,
  createRootRouteWithContext,
  useRouter,
  HeadContent,
  Scripts,
  Link,
} from "@tanstack/react-router";

import appCss from "../styles.css?url";
import { CartProvider } from "@/context/CartContext";
import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";

function NotFoundComponent() {
  return (
    <div className="flex min-h-[60vh] items-center justify-center px-4">
      <div className="max-w-md text-center">
        <h1 className="text-7xl font-bold text-primary">404</h1>
        <h2 className="mt-4 text-xl font-semibold text-foreground">Página não encontrada</h2>
        <p className="mt-2 text-sm text-muted-foreground">
          A página que você procura não existe ou foi movida.
        </p>
        <Link
          to="/"
          className="mt-6 inline-flex items-center justify-center rounded-full bg-primary px-6 h-11 text-sm font-medium text-primary-foreground hover:opacity-90"
        >
          Voltar para o início
        </Link>
      </div>
    </div>
  );
}

function ErrorComponent({ error, reset }: { error: Error; reset: () => void }) {
  console.error(error);
  const router = useRouter();
  return (
    <div className="flex min-h-[60vh] items-center justify-center px-4">
      <div className="max-w-md text-center">
        <h1 className="text-xl font-semibold text-foreground">Esta página não carregou</h1>
        <p className="mt-2 text-sm text-muted-foreground">
          Algo deu errado. Tente novamente ou volte ao início.
        </p>
        <div className="mt-6 flex flex-wrap justify-center gap-2">
          <button
            onClick={() => {
              router.invalidate();
              reset();
            }}
            className="rounded-full bg-primary px-6 h-11 text-sm font-medium text-primary-foreground hover:opacity-90"
          >
            Tentar novamente
          </button>
          <a
            href="/"
            className="rounded-full border border-input bg-white px-6 h-11 inline-flex items-center text-sm font-medium hover:bg-secondary"
          >
            Início
          </a>
        </div>
      </div>
    </div>
  );
}

export const Route = createRootRouteWithContext<{ queryClient: QueryClient }>()({
  head: () => ({
    meta: [
      { charSet: "utf-8" },
      { name: "viewport", content: "width=device-width, initial-scale=1" },
      { title: "LaBella — Moda feminina" },
      {
        name: "description",
        content: "LaBella: vitrine de moda feminina. Escolha suas peças e envie sua solicitação.",
      },
      { name: "theme-color", content: "#FF008C" },
      { name: "msapplication-TileColor", content: "#FF008C" },
      { property: "og:title", content: "LaBella — Moda feminina" },
      { property: "og:description", content: "Moda feminina delicada, moderna e elegante." },
      { property: "og:type", content: "website" },
    ],
    links: [
      { rel: "stylesheet", href: appCss },
      { rel: "icon", type: "image/svg+xml", href: "/favicons/icon.svg" },
      { rel: "icon", type: "image/png", sizes: "32x32", href: "/favicons/favicon-32x32.png" },
      { rel: "icon", type: "image/png", sizes: "16x16", href: "/favicons/favicon-16x16.png" },
      { rel: "apple-touch-icon", sizes: "180x180", href: "/favicons/apple-touch-icon.png" },
      { rel: "manifest", href: "/favicons/site.webmanifest" },
      { rel: "mask-icon", href: "/favicons/icon.svg", color: "#FF008C" },
    ],
  }),
  shellComponent: RootShell,
  component: RootComponent,
  notFoundComponent: NotFoundComponent,
  errorComponent: ErrorComponent,
});

function RootShell({ children }: { children: React.ReactNode }) {
  return (
    <html lang="pt-BR">
      <head>
        <HeadContent />
      </head>
      <body>
        {children}
        <Scripts />
      </body>
    </html>
  );
}

function RootComponent() {
  const { queryClient } = Route.useRouteContext();
  return (
    <QueryClientProvider client={queryClient}>
      <CartProvider>
        <div className="flex min-h-screen flex-col bg-background">
          <Header />
          <main className="flex-1">
            <Outlet />
          </main>
          <Footer />
        </div>
      </CartProvider>
    </QueryClientProvider>
  );
}
