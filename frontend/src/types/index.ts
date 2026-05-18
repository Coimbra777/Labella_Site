export interface Category {
  id: number;
  name: string;
  slug?: string;
}

export interface ProductImage {
  id?: number;
  url: string;
}

export interface Product {
  id: number;
  name: string;
  description?: string;
  short_description?: string;
  price: number;
  /** Caminho ou URL principal (API Laravel preenche junto com `images`). */
  main_image?: string | null;
  image?: string;
  images?: ProductImage[] | string[];
  category_id?: number;
  category?: Category;
  sizes?: string[] | Array<string | { size?: string }>;
  colors?: string[] | Array<string | { color?: string }>;
}

export interface Paginated<T> {
  data: T[];
  current_page?: number;
  last_page?: number;
  per_page?: number;
  total?: number;
}

/** Resposta bruta de GET /api/v1/settings (formato Laravel). */
export interface SettingsApiResponse {
  contact: {
    instagram?: string;
    instagramUrl?: string;
    email?: string;
    phone?: string;
    whatsapp?: string;
    address?: string;
  };
  social: {
    facebook?: string;
    instagram?: string;
    pinterest?: string;
    whatsapp?: string;
  };
  cities?: unknown;
  paymentMethods?: unknown;
  paymentIcons?: unknown;
}

export interface CityOption {
  value: string;
  label: string;
}

/**
 * Configuração normalizada para Header, Footer, formulários e contato.
 */
export interface NormalizedSiteSettings {
  phone?: string;
  /** Apenas dígitos (ex.: 5511999999999) para wa.me */
  whatsapp?: string;
  email?: string;
  address?: string;
  instagram?: string;
  instagramUrl?: string;
  facebook?: string;
  cities: CityOption[];
  paymentMethods: unknown[];
  paymentIcons: unknown[];
  /** Reservado se o painel passar texto institucional no futuro */
  about_text?: string;
}

export interface CartItem {
  product_id: number;
  name: string;
  price: number;
  image: string;
  quantity: number;
  size?: string | null;
  color?: string | null;
}

export interface OrderItemPayload {
  product_id: number;
  quantity: number;
  size?: string | null;
  color?: string | null;
}

export interface OrderRequestPayload {
  customer_name: string;
  customer_phone: string;
  customer_email?: string;
  shipping_city: string;
  shipping_address?: string;
  notes?: string;
  items: OrderItemPayload[];
}

/** Corpo de resposta de POST /api/v1/orders (sucesso). */
export interface CreateOrderApiBody {
  message?: string;
  order?: {
    id?: number;
    order_number?: string;
    [key: string]: unknown;
  };
}

/** Retorno do service após criar pedido (apenas dados seguros para o front). */
export interface OrderCreateResult {
  message?: string;
  order_number?: string;
  id?: number;
}
