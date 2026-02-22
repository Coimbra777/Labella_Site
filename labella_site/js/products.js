document.addEventListener("DOMContentLoaded", async () => {
  const API_BASE = typeof LABELLA_CONFIG !== "undefined" ? LABELLA_CONFIG.API_BASE_URL : "http://localhost:8000";
  const container = document.querySelector("#produtos-container");
  const btnCarregar = document.querySelector("#btn-carregar");
  const filterContainer = document.getElementById("filter-categorias");
  let paginaAtual = 1;
  const porPagina = 8; // quantidade por página
  let isotope;

  async function carregarCategorias() {
    if (!filterContainer) return;
    try {
      const res = await fetch(`${API_BASE}/api/v1/categories`);
      if (!res.ok) return;
      const categorias = await res.json();
      let html = "<button class=\"stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 how-active1\" data-filter=\"*\">Todos</button>";
      categorias.forEach((cat) => {
        const slug = cat.slug || (cat.name || "").toLowerCase().replace(/\s+/g, "-") || "";
        const nome = cat.name || "Categoria";
        html += `<button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5" data-filter=".${slug}">${nome}</button>`;
      });
      filterContainer.innerHTML = html;
    } catch (error) {
      console.error("Erro ao carregar categorias:", error);
    }
  }

  async function carregarProdutos(pagina = 1) {
    if (!container) return;
    try {
      const apiUrl = `${API_BASE}/api/v1/products?page=${pagina}&per_page=${porPagina}`;
      const res = await fetch(apiUrl);

      if (!res.ok) {
        throw new Error(`Erro na API: ${res.status}`);
      }

      const json = await res.json();
      const produtos = json.data || json;

      produtos.forEach((produto) => {
        let imagem = produto.main_image || produto.images?.[0] || "images/placeholder.png";
        if (imagem && imagem.startsWith("/") && !imagem.startsWith("//")) {
          imagem = API_BASE + imagem;
        }
        const categoriaSlug = produto.category?.slug || produto.category?.name || "";
        const preco = parseFloat(produto.price) || 0;
        const produtoUrl = `product-detail.html?id=${produto.id}`;

        const item = document.createElement("div");
        item.className = `col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item ${categoriaSlug}`;

        item.innerHTML = `
            <div class="block2">
              <div class="block2-pic hov-img0">
                <img src="${imagem}" alt="${produto.name}" />
                <a href="${produtoUrl}" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                  Visualizar
                </a>
              </div>
              <div class="block2-txt flex-w flex-t p-t-14">
                <div class="block2-txt-child1 flex-col-l">
                  <a href="${produtoUrl}" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                    ${produto.name}
                  </a>
                  <span class="stext-105 cl3">R$ ${preco.toFixed(2)}</span>
                </div>
                <div class="block2-txt-child2 flex-r p-t-3">
                  <button type="button" class="btn-addcart-b2 flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                    <i class="zmdi zmdi-shopping-cart m-r-6"></i>Adicionar
                  </button>
                </div>
              </div>
            </div>
          `;

        const addBtn = item.querySelector(".btn-addcart-b2");
        if (addBtn && window.LabellaCart) {
          const prod = { id: produto.id, name: produto.name, price: produto.price, main_image: produto.main_image, images: produto.images };
          addBtn.addEventListener("click", (e) => {
            e.preventDefault();
            window.LabellaCart.addItem(prod);
            if (typeof swal !== "undefined") {
              swal(prod.name, "Adicionado ao carrinho!", "success");
            } else {
              alert("Adicionado ao carrinho!");
            }
          });
        }

        container.appendChild(item);
      });

      // Inicializa ou atualiza Isotope
      if (!isotope) {
        isotope = new Isotope(container, {
          itemSelector: ".isotope-item",
          layoutMode: "fitRows",
        });
      } else {
        isotope.reloadItems();
        isotope.arrange();
      }
    } catch (error) {
      console.error("Erro ao carregar produtos:", error);
    }
  }

  function bindFiltros() {
    const botoesFiltro = document.querySelectorAll("#filter-categorias [data-filter]");
    botoesFiltro.forEach((btn) => {
      btn.addEventListener("click", function () {
        const filtro = this.getAttribute("data-filter");
        botoesFiltro.forEach((b) => b.classList.remove("how-active1"));
        this.classList.add("how-active1");
        isotope?.arrange({ filter: filtro });
      });
    });
  }

  await carregarCategorias();
  await carregarProdutos(paginaAtual);
  bindFiltros();

  btnCarregar?.addEventListener("click", (e) => {
    e.preventDefault();
    paginaAtual++;
    carregarProdutos(paginaAtual);
  });
});
