/**
 * Achata erros de validação Laravel (422) para exibição no formulário.
 * Garante uma chave `items` quando houver erro em `items` ou `items.*`.
 */
export function flattenLaravelValidationErrors(
  errors: Record<string, unknown> | undefined | null,
): Record<string, string> {
  const out: Record<string, string> = {};
  if (!errors || typeof errors !== "object") return out;

  let firstItemsMsg: string | undefined;

  for (const [key, raw] of Object.entries(errors)) {
    const msg = firstErrorMessage(raw);
    if (!msg) continue;
    out[key] = msg;
    if (key === "items" || key.startsWith("items.")) {
      firstItemsMsg = firstItemsMsg ?? msg;
    }
  }

  if (firstItemsMsg && !out.items) {
    out.items = firstItemsMsg;
  }

  return out;
}

function firstErrorMessage(raw: unknown): string | undefined {
  if (Array.isArray(raw) && raw.length > 0) {
    const v = raw[0];
    return typeof v === "string" ? v : String(v);
  }
  if (typeof raw === "string") return raw;
  return undefined;
}
