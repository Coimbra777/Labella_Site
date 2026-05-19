export function isValidEmail(email: string): boolean {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());
}

export function isNotEmpty(v: string | undefined | null): boolean {
  return !!v && v.trim().length > 0;
}

export interface OrderFormErrors {
  customer_name?: string;
  customer_phone?: string;
  customer_email?: string;
  shipping_city?: string;
}

export function validateOrderForm(values: {
  customer_name: string;
  customer_phone: string;
  customer_email?: string;
  shipping_city: string;
}): OrderFormErrors {
  const errors: OrderFormErrors = {};
  if (!isNotEmpty(values.customer_name)) errors.customer_name = "Informe seu nome";
  if (!isNotEmpty(values.customer_phone)) errors.customer_phone = "Informe seu telefone";
  if (!isNotEmpty(values.shipping_city)) errors.shipping_city = "Informe a cidade";
  if (values.customer_email && !isValidEmail(values.customer_email))
    errors.customer_email = "E-mail inválido";
  return errors;
}
