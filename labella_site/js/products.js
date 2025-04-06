document.addEventListener("DOMContentLoaded", () => {
  const container = document.querySelector("#produtos-container");
  const btnCarregar = document.querySelector("#btn-carregar");
  let paginaAtual = 1;
  const porPagina = 8; // quantidade por página
  let isotope;

  async function carregarProdutos(pagina = 1) {
    try {
      const res = await fetch(
        `https://seudominio.com/api/produtos?page=${pagina}&limit=${porPagina}`
      );
      const produtos = await res.json();

      produtos.forEach((produto) => {
        const item = document.createElement("div");
        item.className = `col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item ${produto.categoria}`;

        item.innerHTML = `
            <div class="block2">
              <div class="block2-pic hov-img0">
                <img src="${produto.imagem}" alt="${produto.nome}" />
                <a href="#" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 js-show-modal1">
                  Visualizar
                </a>
              </div>
              <div class="block2-txt flex-w flex-t p-t-14">
                <div class="block2-txt-child1 flex-col-l">
                  <a href="product-detail.html" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                    ${produto.nome}
                  </a>
                  <span class="stext-105 cl3">R$ ${produto.preco.toFixed(
                    2
                  )}</span>
                </div>
              </div>
            </div>
          `;

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

  // Carregar página 1 ao iniciar
  carregarProdutos(paginaAtual);

  // Botão "Carregar mais"
  btnCarregar?.addEventListener("click", (e) => {
    e.preventDefault();
    paginaAtual++;
    carregarProdutos(paginaAtual);
  });

  // Filtros com Isotope
  const botoesFiltro = document.querySelectorAll("[data-filter]");
  botoesFiltro.forEach((btn) => {
    btn.addEventListener("click", function () {
      const filtro = this.getAttribute("data-filter");
      botoesFiltro.forEach((b) => b.classList.remove("how-active1"));
      this.classList.add("how-active1");
      isotope?.arrange({ filter: filtro });
    });
  });
});
