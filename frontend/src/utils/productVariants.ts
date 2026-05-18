/**
 * Normaliza tamanhos/cores vindos da API (strings ou objetos { size } / { color }).
 */
export function normalizeSizes(raw: unknown): string[] {
  if (!Array.isArray(raw)) return [];
  const out: string[] = [];
  for (const x of raw) {
    if (typeof x === "string") {
      const t = x.trim();
      if (t) out.push(t);
    } else if (x && typeof x === "object" && "size" in x) {
      const s = String((x as { size: unknown }).size ?? "").trim();
      if (s) out.push(s);
    }
  }
  return out;
}

export function normalizeColors(raw: unknown): string[] {
  if (!Array.isArray(raw)) return [];
  const out: string[] = [];
  for (const x of raw) {
    if (typeof x === "string") {
      const t = x.trim();
      if (t) out.push(t);
    } else if (x && typeof x === "object" && "color" in x) {
      const c = String((x as { color: unknown }).color ?? "").trim();
      if (c) out.push(c);
    }
  }
  return out;
}
