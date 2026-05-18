import { cn } from "@/lib/utils";
import { PillButton } from "./PillButton";

export function SectionTitle({
  title,
  subtitle,
  className,
  align = "center",
}: {
  title: string;
  subtitle?: string;
  className?: string;
  align?: "left" | "center";
}) {
  return (
    <div
      className={cn(
        "flex flex-col gap-2",
        align === "center" ? "items-center text-center" : "items-start text-left",
        className,
      )}
    >
      <h2 className="text-2xl md:text-3xl font-semibold text-foreground tracking-tight">
        {title}
      </h2>
      {subtitle && <p className="text-muted-foreground max-w-xl">{subtitle}</p>}
      <span className="h-1 w-12 rounded-full bg-primary mt-1" />
    </div>
  );
}

export function LoadingSkeleton({
  count = 6,
  className,
}: {
  count?: number;
  className?: string;
}) {
  return (
    <div
      className={cn(
        "grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6",
        className,
      )}
    >
      {Array.from({ length: count }).map((_, i) => (
        <div key={i} className="rounded-2xl bg-white p-3 animate-pulse">
          <div className="aspect-square w-full rounded-xl bg-secondary" />
          <div className="h-4 mt-3 rounded bg-secondary w-3/4" />
          <div className="h-4 mt-2 rounded bg-secondary w-1/3" />
        </div>
      ))}
    </div>
  );
}

export function EmptyState({
  title = "Nada por aqui ainda",
  description,
  action,
}: {
  title?: string;
  description?: string;
  action?: React.ReactNode;
}) {
  return (
    <div className="flex flex-col items-center justify-center text-center py-16 px-4 bg-white rounded-2xl">
      <div className="h-14 w-14 rounded-full bg-secondary flex items-center justify-center text-primary text-2xl">
        ♡
      </div>
      <h3 className="mt-4 text-lg font-semibold text-foreground">{title}</h3>
      {description && (
        <p className="mt-1 text-sm text-muted-foreground max-w-sm">{description}</p>
      )}
      {action && <div className="mt-4">{action}</div>}
    </div>
  );
}

export function ErrorState({
  title = "Algo deu errado",
  description = "Não foi possível carregar agora. Tente novamente.",
  onRetry,
}: {
  title?: string;
  description?: string;
  onRetry?: () => void;
}) {
  return (
    <div className="flex flex-col items-center justify-center text-center py-16 px-4 bg-white rounded-2xl border border-destructive/30">
      <h3 className="text-lg font-semibold text-destructive">{title}</h3>
      <p className="mt-1 text-sm text-muted-foreground max-w-sm">{description}</p>
      {onRetry && (
        <PillButton onClick={onRetry} variant="outline" size="sm" className="mt-4">
          Tentar novamente
        </PillButton>
      )}
    </div>
  );
}