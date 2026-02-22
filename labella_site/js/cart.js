/**
 * Carrinho - Labella (localStorage)
 */
const LABELLA_CART_KEY = "labella_cart";

function getCart() {
  try {
    const data = localStorage.getItem(LABELLA_CART_KEY);
    return data ? JSON.parse(data) : [];
  } catch {
    return [];
  }
}

function setCart(items) {
  localStorage.setItem(LABELLA_CART_KEY, JSON.stringify(items));
  window.dispatchEvent(new CustomEvent("cart:updated"));
}

function addItem(product) {
  const cart = getCart();
  const existing = cart.find((i) => i.product_id === product.id);
  if (existing) {
    existing.quantity += 1;
  } else {
    cart.push({
      product_id: product.id,
      name: product.name,
      price: parseFloat(product.price) || 0,
      image: product.main_image || product.images?.[0] || "",
      quantity: 1,
      size: null,
      color: null,
    });
  }
  setCart(cart);
}

function removeItem(productId) {
  setCart(getCart().filter((i) => i.product_id !== productId));
}

function clearCart() {
  setCart([]);
}

function updateQuantity(productId, quantity) {
  const cart = getCart();
  const item = cart.find((i) => i.product_id === productId);
  if (!item) return;
  if (quantity <= 0) {
    removeItem(productId);
    return;
  }
  item.quantity = quantity;
  setCart(cart);
}

function getTotalCount() {
  return getCart().reduce((sum, i) => sum + i.quantity, 0);
}

function getTotal() {
  return getCart().reduce((sum, i) => sum + i.price * i.quantity, 0);
}

window.LabellaCart = { getCart, addItem, removeItem, updateQuantity, getTotalCount, getTotal, clearCart };

function updateCartBadge() {
  const count = getTotalCount();
  document.querySelectorAll(".js-show-cart").forEach((el) => {
    el.setAttribute("data-notify", count);
  });
}

function renderCartPanel() {
  const container = document.getElementById("cart-panel-content");
  const totalEl = document.getElementById("cart-panel-total");
  if (!container) return;

  const cart = getCart();
  if (cart.length === 0) {
    container.innerHTML = '<p class="stext-108 cl6 p-lr-20">Carrinho vazio</p>';
  } else {
    const API_BASE = typeof LABELLA_CONFIG !== "undefined" ? LABELLA_CONFIG.API_BASE_URL : "";
    container.innerHTML = `
      <ul class="header-cart-wrapitem w-full">
        ${cart
          .map(
            (item) => {
              const img = item.image?.startsWith("/") && API_BASE ? API_BASE + item.image : item.image || "images/placeholder.png";
              return `
              <li class="header-cart-item flex-w flex-t m-b-12" data-product-id="${item.product_id}">
                <div class="header-cart-item-img">
                  <img src="${img}" alt="${item.name}" />
                </div>
                <div class="header-cart-item-txt p-t-8">
                  <a href="product-detail.html?id=${item.product_id}" class="header-cart-item-name m-b-18 hov-cl1 trans-04">${item.name}</a>
                  <span class="header-cart-item-info">${item.quantity} x R$ ${item.price.toFixed(2)}</span>
                  <button type="button" class="cart-remove-item stext-108 cl6 hov-cl1 p-t-4" data-id="${item.product_id}">Remover</button>
                </div>
              </li>`;
            }
          )
          .join("")}
      </ul>
    `;
    container.querySelectorAll(".cart-remove-item").forEach((btn) => {
      btn.addEventListener("click", () => {
        removeItem(parseInt(btn.dataset.id));
      });
    });
  }

  if (totalEl) {
    totalEl.textContent = `Total: R$ ${getTotal().toFixed(2)}`;
  }
  updateCartBadge();
}

document.addEventListener("DOMContentLoaded", () => {
  updateCartBadge();
  renderCartPanel();
  window.addEventListener("cart:updated", () => {
    updateCartBadge();
    renderCartPanel();
  });
});
