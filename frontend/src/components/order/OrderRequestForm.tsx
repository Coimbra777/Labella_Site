import { useState } from "react";
import { useNavigate } from "@tanstack/react-router";
import { Input, Select, Textarea } from "@/components/ui/TextField";
import { PillButton } from "@/components/ui/PillButton";
import { useCart } from "@/context/CartContext";
import { useSettings } from "@/hooks/useSettings";
import { useOrderRequest } from "@/hooks/useOrderRequest";
import { validateOrderForm, type OrderFormErrors } from "@/utils/validators";
import { ApiError } from "@/services/api";
import type { OrderRequestPayload } from "@/types";
import { SESSION_LAST_ORDER_NUMBER_KEY } from "@/constants/cart";
import { flattenLaravelValidationErrors } from "@/utils/laravelErrors";

interface FormState {
  customer_name: string;
  customer_phone: string;
  customer_email: string;
  shipping_city: string;
  shipping_address: string;
  notes: string;
}

const initial: FormState = {
  customer_name: "",
  customer_phone: "",
  customer_email: "",
  shipping_city: "",
  shipping_address: "",
  notes: "",
};

export function OrderRequestForm() {
  const [values, setValues] = useState<FormState>(initial);
  const [errors, setErrors] = useState<OrderFormErrors>({});
  const [serverError, setServerError] = useState<string | null>(null);
  const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});
  const { items, clear } = useCart();
  const { data: settings } = useSettings();
  const mutation = useOrderRequest();
  const navigate = useNavigate();

  const cities = settings?.cities?.length ? settings.cities : [];

  const update = <K extends keyof FormState>(k: K, v: FormState[K]) =>
    setValues((p) => ({ ...p, [k]: v }));

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    if (mutation.isPending) return;
    setServerError(null);
    setFieldErrors({});

    if (items.length === 0) {
      setServerError("Adicione produtos ao carrinho antes de enviar a solicitação.");
      return;
    }

    const v = validateOrderForm(values);
    setErrors(v);
    if (Object.keys(v).length > 0) return;

    const payload: OrderRequestPayload = {
      customer_name: values.customer_name.trim(),
      customer_phone: values.customer_phone.trim(),
      customer_email: values.customer_email.trim() || undefined,
      shipping_city: values.shipping_city.trim(),
      shipping_address: values.shipping_address.trim() || undefined,
      notes: values.notes.trim() || undefined,
      items: items.map((i) => ({
        product_id: i.product_id,
        quantity: i.quantity,
        size: i.size ?? null,
        color: i.color ?? null,
      })),
    };

    try {
      const res = await mutation.mutateAsync(payload);
      const orderNum = res.order_number ?? (res.id != null ? String(res.id) : undefined);

      try {
        if (orderNum) sessionStorage.setItem(SESSION_LAST_ORDER_NUMBER_KEY, orderNum);
      } catch {
        /* storage indisponível */
      }

      clear();

      navigate({
        to: "/confirmacao",
        search: orderNum ? { n: orderNum } : {},
      });
    } catch (err) {
      if (err instanceof ApiError) {
        if (err.status === 422) {
          const data = err.data as {
            errors?: Record<string, unknown>;
            message?: string;
          } | null;
          if (data?.errors && typeof data.errors === "object") {
            setFieldErrors(flattenLaravelValidationErrors(data.errors));
          }
          setServerError(data?.message || "Verifique os dados informados.");
        } else if (err.status === 429) {
          setServerError("Muitas tentativas. Aguarde um instante e tente novamente.");
        } else if (err.status === 0) {
          setServerError(
            "Não foi possível enviar sua solicitação agora. Verifique sua conexão ou tente novamente em alguns instantes.",
          );
        } else {
          setServerError(err.message || "Não foi possível enviar sua solicitação.");
        }
      } else {
        setServerError("Erro inesperado. Tente novamente.");
      }
    }
  }

  return (
    <form onSubmit={onSubmit} className="bg-white rounded-2xl p-5 sm:p-6 space-y-4">
      <p className="text-sm text-muted-foreground bg-secondary rounded-xl p-3">
        Você ainda não está finalizando uma compra. Após o envio, nossa equipe entrará em contato
        para confirmar disponibilidade, entrega e forma de pagamento.
      </p>

      <div className="grid sm:grid-cols-2 gap-4">
        <Input
          name="customer_name"
          label="Nome completo *"
          value={values.customer_name}
          onChange={(e) => update("customer_name", e.target.value)}
          error={errors.customer_name || fieldErrors.customer_name}
          autoComplete="name"
          maxLength={120}
        />
        <Input
          name="customer_phone"
          label="Telefone / WhatsApp *"
          value={values.customer_phone}
          onChange={(e) => update("customer_phone", e.target.value)}
          error={errors.customer_phone || fieldErrors.customer_phone}
          inputMode="tel"
          autoComplete="tel"
          maxLength={20}
          placeholder="(85) 99999-9999"
        />
        <Input
          name="customer_email"
          label="E-mail (opcional)"
          value={values.customer_email}
          onChange={(e) => update("customer_email", e.target.value)}
          error={errors.customer_email || fieldErrors.customer_email}
          type="email"
          autoComplete="email"
          maxLength={150}
        />
        {cities.length > 0 ? (
          <Select
            name="shipping_city"
            label="Cidade *"
            value={values.shipping_city}
            onChange={(e) => update("shipping_city", e.target.value)}
            error={errors.shipping_city || fieldErrors.shipping_city}
          >
            <option value="">Selecione</option>
            {cities.map((c) => (
              <option key={c.value} value={c.value}>
                {c.label}
              </option>
            ))}
          </Select>
        ) : (
          <Input
            name="shipping_city"
            label="Cidade *"
            value={values.shipping_city}
            onChange={(e) => update("shipping_city", e.target.value)}
            error={errors.shipping_city || fieldErrors.shipping_city}
            maxLength={80}
          />
        )}
      </div>

      <Input
        name="shipping_address"
        label="Endereço (opcional)"
        value={values.shipping_address}
        onChange={(e) => update("shipping_address", e.target.value)}
        error={fieldErrors.shipping_address}
        maxLength={200}
      />

      <Textarea
        name="notes"
        label="Observações (opcional)"
        value={values.notes}
        onChange={(e) => update("notes", e.target.value)}
        error={fieldErrors.notes}
        maxLength={500}
        placeholder="Algum detalhe sobre o pedido?"
      />

      {fieldErrors.items && (
        <div className="text-sm text-destructive bg-destructive/10 rounded-xl p-3">
          {fieldErrors.items}
        </div>
      )}

      {serverError && (
        <div className="text-sm text-destructive bg-destructive/10 rounded-xl p-3">
          {serverError}
        </div>
      )}

      <PillButton
        type="submit"
        size="lg"
        className="w-full"
        disabled={mutation.isPending || items.length === 0}
      >
        {mutation.isPending ? "Enviando..." : "Enviar solicitação"}
      </PillButton>
    </form>
  );
}
