import type { Product } from "@/types";

const FALLBACK =
  "data:image/svg+xml;utf8," +
  encodeURIComponent(
    `<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 400'><rect fill='#FCE4EC' width='400' height='400'/><text x='50%' y='50%' font-family='sans-serif' font-size='22' fill='#FF008C' text-anchor='middle' dominant-baseline='middle'>LaBella</text></svg>`,
  );

type ProductImagePick = Pick<Product, "main_image" | "image" | "images">;

export function getProductImage(product: ProductImagePick): string {
  if (product.main_image && typeof product.main_image === "string") return product.main_image;
  if (product.image) return product.image;
  const imgs = product.images;
  if (Array.isArray(imgs) && imgs.length > 0) {
    const first = imgs[0];
    if (typeof first === "string") return first;
    if (first && typeof first === "object" && "url" in first) return first.url;
  }
  return FALLBACK;
}

export function getProductImages(product: ProductImagePick): string[] {
  const out: string[] = [];
  const imgs = product.images;
  if (Array.isArray(imgs)) {
    for (const i of imgs) {
      if (typeof i === "string") out.push(i);
      else if (i && typeof i === "object" && "url" in i) out.push(i.url);
    }
  }
  if (out.length === 0 && product.main_image && typeof product.main_image === "string") {
    out.push(product.main_image);
  }
  if (out.length === 0 && product.image) out.push(product.image);
  if (out.length === 0) out.push(FALLBACK);
  return out;
}

export { FALLBACK as FALLBACK_IMAGE };
