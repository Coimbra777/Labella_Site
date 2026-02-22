/**
 * Página do carrinho - renderiza itens do localStorage
 */
document.addEventListener("DOMContentLoaded", () => {
  const tbody = document.getElementById("cart-table-body");
  const subtotalEl = document.getElementById("cart-subtotal");
  const totalEl = document.getElementById("cart-total");

  function render() {
    const cart = window.LabellaCart ? window.LabellaCart.getCart() : [];
    const API_BASE = typeof LABELLA_CONFIG !== "undefined" ? LABELLA_CONFIG.API_BASE_URL : "";

    if (!tbody) return;

    if (cart.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="5" class="p-t-20 p-b-20 stext-108 cl6 txt-center">Carrinho vazio. <a href="product.html" class="hov-cl1">Ver produtos</a></td>
        </tr>
      `;
    } else {
      tbody.innerHTML = cart
        .map(
          (item) => {
            const img = item.image?.startsWith("/") && API_BASE ? API_BASE + item.image : item.image || "images/placeholder.png";
            const itemTotal = (item.price * item.quantity).toFixed(2);
            return `
            <tr class="table_row" data-product-id="${item.product_id}">
              <td class="column-1">
                <div class="how-itemcart1">
                  <img src="${img}" alt="${item.name}" />
                </div>
              </td>
              <td class="column-2">${item.name}</td>
              <td class="column-3">R$ ${item.price.toFixed(2)}</td>
              <td class="column-4">
                <div class="wrap-num-product flex-w m-l-auto m-r-0">
                  <div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m"><i class="fs-16 zmdi zmdi-minus"></i></div>
                  <input class="mtext-104 cl3 txt-center num-product" type="number" value="${item.quantity}" min="1" data-id="${item.product_id}" />
                  <div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m"><i class="fs-16 zmdi zmdi-plus"></i></div>
                </div>
              </td>
              <td class="column-5">R$ ${itemTotal}</td>
            </tr>
          `;
          }
        )
        .join("");

      tbody.querySelectorAll(".btn-num-product-down").forEach((btn) => {
        btn.addEventListener("click", () => {
          const input = btn.closest("tr").querySelector(".num-product");
          const qty = Math.max(1, parseInt(input.value) - 1);
          window.LabellaCart?.updateQuantity(parseInt(input.dataset.id), qty);
        });
      });
      tbody.querySelectorAll(".btn-num-product-up").forEach((btn) => {
        btn.addEventListener("click", () => {
          const input = btn.closest("tr").querySelector(".num-product");
          const qty = parseInt(input.value) + 1;
          window.LabellaCart?.updateQuantity(parseInt(input.dataset.id), qty);
        });
      });
      tbody.querySelectorAll(".num-product").forEach((input) => {
        input.addEventListener("change", () => {
          const qty = Math.max(1, parseInt(input.value) || 1);
          window.LabellaCart?.updateQuantity(parseInt(input.dataset.id), qty);
        });
      });
    }

    const total = window.LabellaCart ? window.LabellaCart.getTotal() : 0;
    if (subtotalEl) subtotalEl.textContent = `R$ ${total.toFixed(2)}`;
    if (totalEl) totalEl.textContent = `R$ ${total.toFixed(2)}`;
  }

  window.addEventListener("cart:updated", render);
  render();
});
