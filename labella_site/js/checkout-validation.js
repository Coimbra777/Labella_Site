/**
 * Validação dos campos do checkout - shoping-cart.html
 */
const CHECKOUT_VALIDATION = {
  fields: [
    { id: "customer_name", label: "Nome completo", required: true },
    { id: "customer_email", label: "E-mail", required: true, type: "email" },
    { id: "customer_phone", label: "Telefone", required: false },
    { id: "shipping_address", label: "Endereço", required: true },
    { id: "shipping_city", label: "Cidade", required: true },
    { id: "shipping_state", label: "Estado", required: true },
    { id: "shipping_zip", label: "CEP", required: true },
  ],

  validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  },

  validate(el, errorsList) {
    const field = this.fields.find((f) => f.id === el.id);
    if (!field || !field.required) return true;

    const value = el.value.trim();
    const wrapper = el.closest(".bor8");

    if (!value) {
      errorsList.push(`${field.label} é obrigatório.`);
      if (wrapper) wrapper.classList.add("checkout-field-error");
      return false;
    }

    if (field.type === "email" && !this.validateEmail(value)) {
      errorsList.push("Informe um e-mail válido.");
      if (wrapper) wrapper.classList.add("checkout-field-error");
      return false;
    }

    if (wrapper) wrapper.classList.remove("checkout-field-error");
    return true;
  },

  validateAll() {
    const errors = [];
    this.fields.forEach((field) => {
      const el = document.getElementById(field.id);
      if (el) this.validate(el, errors);
    });
    return errors;
  },

  clearErrors() {
    this.fields.forEach((field) => {
      const el = document.getElementById(field.id);
      const wrapper = el?.closest(".bor8");
      if (wrapper) wrapper.classList.remove("checkout-field-error");
    });
  },
};
