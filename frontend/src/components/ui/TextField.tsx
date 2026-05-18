import { forwardRef } from "react";
import { cn } from "@/lib/utils";

interface BaseProps {
  label?: string;
  error?: string;
  hint?: string;
  containerClassName?: string;
}

export type InputProps = BaseProps &
  React.InputHTMLAttributes<HTMLInputElement>;

const inputCls =
  "w-full h-11 rounded-full border border-input bg-white px-4 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-primary transition";

export const Input = forwardRef<HTMLInputElement, InputProps>(
  ({ label, error, hint, containerClassName, className, id, ...props }, ref) => {
    const inputId = id || props.name;
    return (
      <div className={cn("flex flex-col gap-1", containerClassName)}>
        {label && (
          <label htmlFor={inputId} className="text-sm font-medium text-foreground">
            {label}
          </label>
        )}
        <input
          id={inputId}
          ref={ref}
          className={cn(inputCls, error && "border-destructive", className)}
          {...props}
        />
        {error ? (
          <span className="text-xs text-destructive">{error}</span>
        ) : hint ? (
          <span className="text-xs text-muted-foreground">{hint}</span>
        ) : null}
      </div>
    );
  },
);
Input.displayName = "Input";

export type TextareaProps = BaseProps &
  React.TextareaHTMLAttributes<HTMLTextAreaElement>;

export const Textarea = forwardRef<HTMLTextAreaElement, TextareaProps>(
  ({ label, error, hint, containerClassName, className, id, ...props }, ref) => {
    const inputId = id || props.name;
    return (
      <div className={cn("flex flex-col gap-1", containerClassName)}>
        {label && (
          <label htmlFor={inputId} className="text-sm font-medium text-foreground">
            {label}
          </label>
        )}
        <textarea
          id={inputId}
          ref={ref}
          className={cn(
            "w-full min-h-24 rounded-2xl border border-input bg-white px-4 py-3 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-primary transition",
            error && "border-destructive",
            className,
          )}
          {...props}
        />
        {error ? (
          <span className="text-xs text-destructive">{error}</span>
        ) : hint ? (
          <span className="text-xs text-muted-foreground">{hint}</span>
        ) : null}
      </div>
    );
  },
);
Textarea.displayName = "Textarea";

export type SelectProps = BaseProps &
  React.SelectHTMLAttributes<HTMLSelectElement>;

export const Select = forwardRef<HTMLSelectElement, SelectProps>(
  ({ label, error, hint, containerClassName, className, id, children, ...props }, ref) => {
    const inputId = id || props.name;
    return (
      <div className={cn("flex flex-col gap-1", containerClassName)}>
        {label && (
          <label htmlFor={inputId} className="text-sm font-medium text-foreground">
            {label}
          </label>
        )}
        <select
          id={inputId}
          ref={ref}
          className={cn(inputCls, "appearance-none pr-10", error && "border-destructive", className)}
          {...props}
        >
          {children}
        </select>
        {error ? (
          <span className="text-xs text-destructive">{error}</span>
        ) : hint ? (
          <span className="text-xs text-muted-foreground">{hint}</span>
        ) : null}
      </div>
    );
  },
);
Select.displayName = "Select";