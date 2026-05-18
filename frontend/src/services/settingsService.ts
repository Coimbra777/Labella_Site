import { api } from "./api";
import type { NormalizedSiteSettings, SettingsApiResponse } from "@/types";

function digitsForWa(input: string | undefined): string | undefined {
  if (!input?.trim()) return undefined;
  const d = input.replace(/\D/g, "");
  return d || undefined;
}

function normalizeCities(raw: unknown): NormalizedSiteSettings["cities"] {
  if (!Array.isArray(raw)) return [];
  const out: NormalizedSiteSettings["cities"] = [];
  for (const c of raw) {
    if (typeof c === "string") {
      const t = c.trim();
      if (t) out.push({ value: t, label: t });
    } else if (c && typeof c === "object") {
      const value = String((c as { value?: unknown }).value ?? "").trim();
      const labelRaw = (c as { label?: unknown }).label;
      const label = labelRaw !== undefined && labelRaw !== null ? String(labelRaw).trim() : value;
      if (value) out.push({ value, label: label || value });
    }
  }
  return out;
}

export function normalizeSettingsResponse(raw: SettingsApiResponse): NormalizedSiteSettings {
  const contact = raw.contact ?? {};
  const social = raw.social ?? {};
  const waContact = digitsForWa(contact.whatsapp);
  const waSocial = digitsForWa(social.whatsapp);
  const instagramUrl = (contact.instagramUrl || social.instagram || "").trim() || undefined;

  return {
    phone: contact.phone?.trim() || undefined,
    whatsapp: waContact || waSocial,
    email: contact.email?.trim() || undefined,
    address: contact.address?.trim() || undefined,
    instagram: contact.instagram?.trim() || undefined,
    instagramUrl,
    facebook: social.facebook?.trim() || undefined,
    cities: normalizeCities(raw.cities),
    paymentMethods: Array.isArray(raw.paymentMethods) ? raw.paymentMethods : [],
    paymentIcons: Array.isArray(raw.paymentIcons) ? raw.paymentIcons : [],
  };
}

function unwrapSettingsPayload(
  res: SettingsApiResponse | { data?: SettingsApiResponse },
): SettingsApiResponse {
  if (
    res &&
    typeof res === "object" &&
    "data" in res &&
    res.data &&
    typeof res.data === "object" &&
    "contact" in res.data
  ) {
    return res.data as SettingsApiResponse;
  }
  return res as SettingsApiResponse;
}

export const settingsService = {
  async get(): Promise<NormalizedSiteSettings> {
    const res = await api.get<SettingsApiResponse | { data: SettingsApiResponse }>(
      "/api/v1/settings",
    );
    return normalizeSettingsResponse(unwrapSettingsPayload(res));
  },
};
