import type { Category } from "@/types";
import { cn } from "@/lib/utils";

interface Props {
  categories: Category[];
  selectedId: number | null;
  onSelect: (id: number | null) => void;
}

export function CategoryFilter({ categories, selectedId, onSelect }: Props) {
  return (
    <div className="flex flex-wrap gap-2 justify-center">
      <Chip active={selectedId === null} onClick={() => onSelect(null)}>
        Todas
      </Chip>
      {categories.map((c) => (
        <Chip
          key={c.id}
          active={selectedId === c.id}
          onClick={() => onSelect(c.id)}
        >
          {c.name}
        </Chip>
      ))}
    </div>
  );
}

function Chip({
  active,
  onClick,
  children,
}: {
  active: boolean;
  onClick: () => void;
  children: React.ReactNode;
}) {
  return (
    <button
      type="button"
      onClick={onClick}
      className={cn(
        "px-4 h-9 rounded-full text-sm font-medium transition border",
        active
          ? "bg-primary text-primary-foreground border-primary"
          : "bg-white text-foreground border-border hover:border-primary hover:text-primary",
      )}
    >
      {children}
    </button>
  );
}