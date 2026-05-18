function requireApiBaseUrl(): string {
  const raw = import.meta.env.VITE_API_BASE_URL;
  if (raw === undefined || raw === null || String(raw).trim() === "") {
    throw new Error(
      "VITE_API_BASE_URL não está definida. Copie .env.example para .env e configure a URL da API Laravel.",
    );
  }
  return String(raw).replace(/\/$/, "");
}

export class ApiError extends Error {
  status: number;
  data: unknown;
  constructor(message: string, status: number, data?: unknown) {
    super(message);
    this.status = status;
    this.data = data;
  }
}

async function request<T>(path: string, init?: RequestInit): Promise<T> {
  const baseUrl = requireApiBaseUrl();
  let res: Response;
  try {
    res = await fetch(`${baseUrl}${path}`, {
      ...init,
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        ...(init?.headers || {}),
      },
    });
  } catch {
    throw new ApiError("Não foi possível conectar ao servidor. Verifique sua conexão.", 0);
  }

  const text = await res.text();
  const data = text ? safeJson(text) : null;

  // 200 e 201 tratados como sucesso (pedidos podem usar 201 Created).
  if (!res.ok) {
    const msg =
      (data && typeof data === "object" && "message" in data
        ? String((data as { message: unknown }).message)
        : null) || `Erro ${res.status}`;
    throw new ApiError(msg, res.status, data);
  }

  return data as T;
}

function safeJson(text: string): unknown {
  try {
    return JSON.parse(text);
  } catch {
    return null;
  }
}

export const api = {
  get: <T>(path: string) => request<T>(path, { method: "GET" }),
  post: <T>(path: string, body: unknown) =>
    request<T>(path, { method: "POST", body: JSON.stringify(body) }),
};

/** Para URLs absolutas de assets da API, se necessário no futuro. */
export function getApiBaseUrl(): string {
  return requireApiBaseUrl();
}
