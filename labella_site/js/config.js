/**
 * Configuração - Labella
 * Ajuste API_BASE_URL conforme o ambiente (desenvolvimento/produção)
 * As configurações do site (contato, redes sociais, pagamentos) são gerenciadas
 * pelo painel em /admin e buscadas da API em /api/v1/settings
 */
const LABELLA_CONFIG = {
  API_BASE_URL: window.LABELLA_API_URL || "http://localhost:8000",
};

/**
 * Fallback quando a API não está disponível (ex: offline, desenvolvimento)
 */
const LABELLA_SITE_CONFIG = {
  contact: {
    instagram: "@labella",
    instagramUrl: "https://instagram.com/labella",
    email: "contato@labella.com.br",
    phone: "(11) 99999-9999",
    whatsapp: "5511999999999",
    address: "São Paulo, SP - Brasil",
  },
  social: {
    facebook: "https://facebook.com/labella",
    instagram: "https://instagram.com/labella",
    pinterest: "",
  },
  cities: [
    { value: "sao-luis", label: "São Luís" },
    { value: "imperatriz", label: "Imperatriz" },
    { value: "caxias", label: "Caxias" },
  ],
  paymentMethods: [
    { value: "pix", label: "PIX" },
    { value: "cartao", label: "Cartão de crédito" },
    { value: "boleto", label: "Boleto" },
    { value: "transferencia", label: "Transferência bancária" },
  ],
  paymentIcons: [
    { src: "images/icons/icon-pay-01.png", alt: "Visa" },
    { src: "images/icons/icon-pay-02.png", alt: "Mastercard" },
    { src: "images/icons/icon-pay-03.png", alt: "Amex" },
    { src: "images/icons/icon-pay-04.png", alt: "PayPal" },
    { src: "images/icons/icon-pay-05.png", alt: "PIX" },
  ],
};
