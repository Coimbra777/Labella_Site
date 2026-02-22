/**
 * Aplica as configurações do site (busca da API ou fallback local)
 * Deve ser carregado após config.js
 */
(function () {
  const API_BASE = typeof LABELLA_CONFIG !== "undefined" ? LABELLA_CONFIG.API_BASE_URL : "http://localhost:8000";
  const FALLBACK = typeof LABELLA_SITE_CONFIG !== "undefined" ? LABELLA_SITE_CONFIG : {};

  function apply(C) {
    const contact = (C && C.contact) || FALLBACK.contact || {};
    const social = (C && C.social) || FALLBACK.social || {};
    const cities = (C && C.cities) || FALLBACK.cities || [];
    const paymentMethods = (C && C.paymentMethods) || FALLBACK.paymentMethods || [];
    const paymentIcons = (C && C.paymentIcons) || FALLBACK.paymentIcons || [];
    // Texto de contato no footer (Instagram + WhatsApp)
    document.querySelectorAll("[data-labella-contact-text]").forEach((el) => {
      const insta = contact.instagram || "@labella";
      const whats = contact.phone || contact.whatsapp || "";
      el.innerHTML = `Alguma dúvida? Fale com a gente pelo Instagram <strong>${insta}</strong>${whats ? ` ou no WhatsApp <strong>${whats}</strong>` : ""}.`;
    });

    // Texto de contato na página Sobre
    document.querySelectorAll("[data-labella-about-contact]").forEach((el) => {
      const insta = contact.instagram || "@labella";
      const whats = contact.phone || contact.whatsapp || "";
      el.innerHTML = `Tem alguma dúvida? Fale com a gente no Instagram <strong>${insta}</strong>${whats ? ` ou pelo WhatsApp <strong>${whats}</strong>` : ""}. Será um prazer te atender!`;
    });

    // Link do Instagram
    document.querySelectorAll("[data-labella-instagram-link]").forEach((el) => {
      el.href = contact.instagramUrl || social.instagram || "https://instagram.com/" + (contact.instagram || "").replace("@", "");
      el.target = "_blank";
      el.rel = "noopener";
    });

    // Link do Facebook
    document.querySelectorAll("[data-labella-facebook-link]").forEach((el) => {
      if (social.facebook) {
        el.href = social.facebook;
        el.target = "_blank";
        el.rel = "noopener";
        el.style.display = "";
      } else el.style.display = "none";
    });

    // Link do Pinterest
    document.querySelectorAll("[data-labella-pinterest-link]").forEach((el) => {
      if (social.pinterest) {
        el.href = social.pinterest;
        el.target = "_blank";
        el.rel = "noopener";
        el.style.display = "";
      } else el.style.display = "none";
    });

    // Endereço
    document.querySelectorAll("[data-labella-address]").forEach((el) => {
      el.textContent = contact.address || "";
    });

    // Telefone
    document.querySelectorAll("[data-labella-phone]").forEach((el) => {
      el.textContent = contact.phone || contact.whatsapp || "";
    });
    document.querySelectorAll("[data-labella-phone-link]").forEach((el) => {
      const tel = (contact.whatsapp || contact.phone || "").replace(/\D/g, "");
      el.href = tel ? "https://wa.me/" + (tel.startsWith("55") ? tel : "55" + tel) : "#";
      if (tel) el.target = "_blank";
    });

    // Email
    document.querySelectorAll("[data-labella-email]").forEach((el) => {
      el.textContent = contact.email || "";
    });
    document.querySelectorAll("[data-labella-email-link]").forEach((el) => {
      el.href = contact.email ? "mailto:" + contact.email : "#";
    });

    // Lista de formas de pagamento (order-confirmation e outras páginas)
    document.querySelectorAll("[data-labella-payment-methods-list]").forEach((el) => {
      if (paymentMethods.length) {
        el.textContent = paymentMethods.map((m) => m.label).join(", ");
      }
    });
    const paymentMethodsContainer = document.getElementById("labella-payment-methods");
    if (paymentMethodsContainer && paymentMethods.length) {
      paymentMethodsContainer.style.display = "block";
    }

    // Container de ícones de pagamento (usa config do painel)
    const payContainer = document.getElementById("labella-payment-icons");
    if (payContainer) {
      payContainer.innerHTML = paymentIcons
        .map(
          (p) =>
            `<a href="#" class="m-all-1" title="${p.alt}"><img src="${p.src}" alt="${p.alt}" /></a>`
        )
        .join("");
    }

    // Select de cidades
    const citySelect = document.getElementById("shipping_city");
    if (citySelect && cities.length) {
      const first = citySelect.querySelector("option[value='']");
      citySelect.innerHTML = first ? first.outerHTML : '<option value="">Selecione a cidade</option>';
      cities.forEach((c) => {
        const opt = document.createElement("option");
        opt.value = c.value;
        opt.textContent = c.label;
        citySelect.appendChild(opt);
      });
    }

    // Select de formas de pagamento
    const paySelect = document.getElementById("payment_method");
    if (paySelect && paymentMethods.length) {
      const first = paySelect.querySelector("option[value='']");
      paySelect.innerHTML = first ? first.outerHTML : '<option value="">Forma de pagamento</option>';
      paymentMethods.forEach((m) => {
        const opt = document.createElement("option");
        opt.value = m.value;
        opt.textContent = m.label;
        paySelect.appendChild(opt);
      });
    }
  }

  function init() {
    fetch(API_BASE + "/api/v1/settings")
      .then((r) => (r.ok ? r.json() : Promise.reject()))
      .then((data) => apply(data))
      .catch(() => apply(null));
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
