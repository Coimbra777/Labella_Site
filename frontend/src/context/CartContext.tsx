import { createContext, useCallback, useContext, useEffect, useMemo, useState } from "react";
import type { CartItem } from "@/types";
import { MAX_QUANTITY_PER_LINE } from "@/constants/cart";

const STORAGE_KEY = "labella_cart";

interface CartContextValue {
  items: CartItem[];
  addItem: (item: CartItem) => void;
  removeItem: (product_id: number, size?: string | null, color?: string | null) => void;
  updateQuantity: (
    product_id: number,
    quantity: number,
    size?: string | null,
    color?: string | null,
  ) => void;
  clear: () => void;
  totalItems: number;
  subtotal: number;
}

const CartContext = createContext<CartContextValue | undefined>(undefined);

function sameLine(
  a: Pick<CartItem, "product_id" | "size" | "color">,
  b: Pick<CartItem, "product_id" | "size" | "color">,
) {
  return (
    a.product_id === b.product_id &&
    (a.size ?? null) === (b.size ?? null) &&
    (a.color ?? null) === (b.color ?? null)
  );
}

function clampQty(n: number): number {
  return Math.min(MAX_QUANTITY_PER_LINE, Math.max(1, Math.floor(n)));
}

function loadInitial(): CartItem[] {
  if (typeof window === "undefined") return [];
  try {
    const raw = window.localStorage.getItem(STORAGE_KEY);
    if (!raw) return [];
    const parsed = JSON.parse(raw) as unknown;
    if (!Array.isArray(parsed)) return [];
    return parsed
      .filter(
        (i): i is CartItem =>
          !!i &&
          typeof i === "object" &&
          typeof (i as CartItem).product_id === "number" &&
          typeof (i as CartItem).quantity === "number",
      )
      .map((i) => ({ ...i, quantity: clampQty((i as CartItem).quantity) }));
  } catch {
    return [];
  }
}

export function CartProvider({ children }: { children: React.ReactNode }) {
  const [items, setItems] = useState<CartItem[]>(() => loadInitial());

  useEffect(() => {
    try {
      window.localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
    } catch {
      /* ignore quota */
    }
  }, [items]);

  const addItem = useCallback((item: CartItem) => {
    const qty = clampQty(item.quantity || 1);
    setItems((prev) => {
      const idx = prev.findIndex((p) => sameLine(p, item));
      if (idx >= 0) {
        const next = [...prev];
        next[idx] = {
          ...next[idx],
          quantity: clampQty(next[idx].quantity + qty),
        };
        return next;
      }
      return [...prev, { ...item, quantity: qty }];
    });
  }, []);

  const removeItem = useCallback(
    (product_id: number, size?: string | null, color?: string | null) => {
      setItems((prev) => prev.filter((p) => !sameLine(p, { product_id, size, color })));
    },
    [],
  );

  const updateQuantity = useCallback(
    (product_id: number, quantity: number, size?: string | null, color?: string | null) => {
      const q = clampQty(quantity);
      setItems((prev) =>
        prev.map((p) => (sameLine(p, { product_id, size, color }) ? { ...p, quantity: q } : p)),
      );
    },
    [],
  );

  const clear = useCallback(() => setItems([]), []);

  const value = useMemo<CartContextValue>(() => {
    const totalItems = items.reduce((s, i) => s + i.quantity, 0);
    const subtotal = items.reduce((s, i) => s + i.quantity * Number(i.price || 0), 0);
    return { items, addItem, removeItem, updateQuantity, clear, totalItems, subtotal };
  }, [items, addItem, removeItem, updateQuantity, clear]);

  return <CartContext.Provider value={value}>{children}</CartContext.Provider>;
}

export function useCart() {
  const ctx = useContext(CartContext);
  if (!ctx) throw new Error("useCart deve ser usado dentro de CartProvider");
  return ctx;
}
