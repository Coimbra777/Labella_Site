/**
 * Checkout - Finalização da compra via API
 */
document.addEventListener("DOMContentLoaded", () => {
  const btnFinalizar = document.getElementById("btn-finalizar");
  if (!btnFinalizar) return;

  const API_BASE = typeof LABELLA_CONFIG !== "undefined" ? LABELLA_CONFIG.API_BASE_URL : "http://localhost:8000";

  function getField(id) {
    const el = document.getElementById(id);
    return el ? el.value.trim() : "";
  }

  function showError(msg) {
    if (typeof swal !== "undefined") {
      swal("Erro", msg, "error");
    } else {
      alert("Erro: " + msg);
    }
  }

  function showSuccess(msg) {
    if (typeof swal !== "undefined") {
      swal("Pedido realizado!", msg, "success");
    } else {
      alert(msg);
    }
  }

  const errorsDiv = document.getElementById("checkout-errors");
  const errorsList = document.getElementById("checkout-errors-list");

  function showFormErrors(messages) {
    if (!errorsDiv || !errorsList) return;
    errorsDiv.style.display = "block";
    errorsList.innerHTML = messages.map((m) => `<li>${m}</li>`).join("");
    errorsDiv.scrollIntoView({ behavior: "smooth", block: "center" });
  }

  function hideFormErrors() {
    if (errorsDiv) errorsDiv.style.display = "none";
    if (errorsList) errorsList.innerHTML = "";
  }

  btnFinalizar.addEventListener("click", async () => {
    hideFormErrors();

    const cart = window.LabellaCart?.getCart() || [];
    if (cart.length === 0) {
      showFormErrors(["Seu carrinho está vazio. Adicione produtos antes de finalizar."]);
      return;
    }

    const customer_name = getField("customer_name");
    const customer_email = getField("customer_email");
    const customer_phone = getField("customer_phone");
    const shipping_address = getField("shipping_address");
    const shipping_city = getField("shipping_city");
    const shipping_state = getField("shipping_state");
    const shipping_zip = getField("shipping_zip");
    const shipping_country = getField("shipping_country") || "BR";
    const payment_method = getField("payment_method");
    const notes = getField("notes");

    const items = cart.map((item) => ({
      product_id: item.product_id,
      quantity: item.quantity,
      size: item.size || null,
      color: item.color || null,
    }));

    const payload = {
      customer_name,
      customer_email,
      customer_phone: customer_phone || null,
      shipping_address,
      shipping_city,
      shipping_state,
      shipping_zip,
      shipping_country,
      items,
      shipping_cost: 0,
      discount: 0,
      payment_method: payment_method || null,
      notes: notes || null,
    };

    btnFinalizar.disabled = true;
    btnFinalizar.textContent = "Processando...";

    try {
      const res = await fetch(`${API_BASE}/api/v1/orders`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify(payload),
      });

      const data = await res.json().catch(() => ({}));

      if (!res.ok) {
        let errList = [];
        if (data.errors && Object.keys(data.errors).length > 0) {
          errList = Object.values(data.errors).flat();
        } else {
          errList = [data.message || data.error || `Erro ${res.status}`];
        }
        showFormErrors(errList);
        btnFinalizar.disabled = false;
        btnFinalizar.textContent = "Finalizar Compra";
        return;
      }

      const orderNumber = data.order?.order_number || data.order_number || "N/A";
      window.LabellaCart?.clearCart();
      showSuccess(`Pedido #${orderNumber} realizado com sucesso! Em breve entraremos em contato.`);
      setTimeout(() => {
        window.location.href = `order-confirmation.html?order=${encodeURIComponent(orderNumber)}`;
      }, 1500);
    } catch (err) {
      showFormErrors([err.message]);
      btnFinalizar.disabled = false;
      btnFinalizar.textContent = "Finalizar Compra";
    }
  });
});
