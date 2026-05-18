import { cn } from "@/lib/utils";

interface LogoProps {
  variant?: "horizontal" | "compact";
  className?: string;
}

export function Logo({ variant = "horizontal", className }: LogoProps) {
  if (variant === "compact") {
    return (
      <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 52 52"
        className={cn("w-10 h-10", className)}
        aria-label="LaBella"
      >
        <rect x="6" y="4" width="4" height="44" rx="2" fill="currentColor" />
        <rect x="6" y="4" width="16" height="4" rx="2" fill="currentColor" />
        <rect x="6" y="44" width="20" height="4" rx="2" fill="currentColor" />
        <path
          d="M 10,8 C 28,8 34,14 34,22 C 34,30 28,34 18,34 L 10,34"
          fill="none"
          stroke="currentColor"
          strokeWidth="4"
          strokeLinecap="round"
        />
        <path
          d="M 10,34 C 28,34 38,38 38,48 C 38,58 28,62 16,62 L 10,62"
          fill="none"
          stroke="currentColor"
          strokeWidth="4"
          strokeLinecap="round"
          transform="translate(0, -20)"
        />
      </svg>
    );
  }

  return (
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 260 60"
      className={cn("h-8 w-auto", className)}
      aria-label="LaBella"
    >
      {/* Monograma LB */}
      <g transform="translate(4, 10)">
        <rect x="0" y="0" width="3" height="40" rx="1.5" fill="currentColor" />
        <rect x="0" y="0" width="12" height="3" rx="1.5" fill="currentColor" />
        <rect x="0" y="37" width="16" height="3" rx="1.5" fill="currentColor" />
        <path
          d="M 3,2 C 18,2 24,8 24,14 C 24,20 18,24 10,24 L 3,24"
          fill="none"
          stroke="currentColor"
          strokeWidth="3"
          strokeLinecap="round"
        />
        <path
          d="M 3,24 C 18,24 28,28 28,36 C 28,44 18,48 8,48 L 3,48"
          fill="none"
          stroke="currentColor"
          strokeWidth="3"
          strokeLinecap="round"
          transform="translate(0, -11)"
        />
      </g>

      {/* Divisor */}
      <rect x="48" y="12" width="1.5" height="36" rx="0.75" fill="currentColor" opacity="0.25" />

      {/* Wordmark LaBella */}
      <g transform="translate(62, 44)">
        {/* L */}
        <path d="M 0,-24 L 0,0 L 12,0" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round" />

        {/* a */}
        <g transform="translate(20, 0)">
          <ellipse cx="6" cy="-6" rx="6" ry="6.5" fill="none" stroke="currentColor" strokeWidth="3" />
          <path d="M 12,-12 L 12,0" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" />
        </g>

        {/* B */}
        <g transform="translate(40, 0)">
          <path d="M 0,-24 L 0,0" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" />
          <path d="M 0,-20 C 12,-20 16,-15 16,-10 C 16,-5 12,-2 0,-2" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" />
          <path d="M 0,-2 C 14,-2 18,2 18,8 C 18,14 12,16 0,16" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" />
        </g>

        {/* e */}
        <g transform="translate(66, 0)">
          <ellipse cx="6" cy="-6" rx="6" ry="6.5" fill="none" stroke="currentColor" strokeWidth="3" />
          <path d="M 0.5,-6 L 11.5,-6" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" />
        </g>

        {/* l */}
        <g transform="translate(84, 0)">
          <path d="M 0,-24 L 0,0" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" />
        </g>

        {/* l */}
        <g transform="translate(92, 0)">
          <path d="M 0,-24 L 0,0" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" />
        </g>

        {/* a */}
        <g transform="translate(100, 0)">
          <ellipse cx="6" cy="-6" rx="6" ry="6.5" fill="none" stroke="currentColor" strokeWidth="3" />
          <path d="M 12,-12 L 12,0" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" />
        </g>
      </g>
    </svg>
  );
}
