/**
 * Validação da página de confirmação de pedido
 * Remove itens do carrinho quando o pedido é confirmado com sucesso
 */
document.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);
  const orderNum = params.get("order");
  const section = document.querySelector(".flex-col-c-m");
  const iconSpan = section?.querySelector("span.mtext-107");
  const titleEl = section?.querySelector("h2");
  const messageEl = document.getElementById("order-message");
  const buttonsDiv = section?.querySelector(".flex-w.flex-c-m");

  if (!section || !messageEl) return;

  if (!orderNum || orderNum.trim() === "") {
    if (iconSpan) iconSpan.innerHTML = '<i class="zmdi zmdi-alert-circle" style="color: #e74c3c;"></i>';
    if (iconSpan) iconSpan.style.fontSize = "4rem";
    if (titleEl) titleEl.textContent = "Pedido não encontrado";
    messageEl.textContent = "O número do pedido não foi informado. Acesse esta página após finalizar uma compra.";
    messageEl.style.color = "#e74c3c";
    messageEl.classList.add("stext-114", "cl6");
    if (buttonsDiv) buttonsDiv.innerHTML = `
      <a href="shoping-cart.html" class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04 m-r-10">
        Ir para o carrinho
      </a>
      <a href="index.html" class="flex-c-m stext-101 cl0 size-101 bg3 bor1 hov-btn3 p-lr-15 trans-04">
        Voltar ao início
      </a>
    `;
  } else {
    messageEl.textContent =
      "Seu pedido #" + orderNum + " foi registrado. Em breve entraremos em contato para confirmar o pagamento e envio.";

    // Remove itens do carrinho após pedido confirmado
    if (window.LabellaCart) {
      window.LabellaCart.clearCart();
    }
  }
});
