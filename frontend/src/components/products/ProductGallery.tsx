import { useState } from "react";
import { cn } from "@/lib/utils";
import { FALLBACK_IMAGE } from "@/utils/productImage";

export function ProductGallery({ images, alt }: { images: string[]; alt: string }) {
  const [active, setActive] = useState(0);
  const list = images.length > 0 ? images : [FALLBACK_IMAGE];
  return (
    <div className="flex flex-col gap-3">
      <div className="aspect-square w-full overflow-hidden rounded-2xl bg-white">
        <img
          src={list[active]}
          alt={alt}
          onError={(e) => {
            (e.currentTarget as HTMLImageElement).src = FALLBACK_IMAGE;
          }}
          className="h-full w-full object-cover"
        />
      </div>
      {list.length > 1 && (
        <div className="flex gap-2 overflow-x-auto">
          {list.map((src, i) => (
            <button
              key={i}
              type="button"
              onClick={() => setActive(i)}
              className={cn(
                "h-16 w-16 shrink-0 rounded-lg overflow-hidden border-2 transition",
                active === i ? "border-primary" : "border-transparent opacity-70",
              )}
            >
              <img src={src} alt="" className="h-full w-full object-cover" />
            </button>
          ))}
        </div>
      )}
    </div>
  );
}