/**
 * Product Detail - Carrega e exibe detalhes do produto via API
 */
document.addEventListener("DOMContentLoaded", async () => {
  const API_BASE =
    typeof LABELLA_CONFIG !== "undefined"
      ? LABELLA_CONFIG.API_BASE_URL
      : "http://localhost:8000";

  const params = new URLSearchParams(window.location.search);
  const productId = params.get("id");

  const loadingEl = document.getElementById("product-detail-loading");
  const errorEl = document.getElementById("product-detail-error");
  const contentEl = document.getElementById("product-detail-content");
  const breadcrumbEl = document.getElementById("breadcrumb-product-name");

  if (!productId) {
    loadingEl.style.display = "none";
    errorEl.style.display = "block";
    return;
  }

  try {
    const res = await fetch(`${API_BASE}/api/v1/products/${productId}`);
    if (!res.ok) {
      throw new Error("Produto não encontrado");
    }

    const produto = await res.json();

    // Atualiza título da página
    document.title = `${produto.name} | Labella`;

    // Breadcrumb
    if (breadcrumbEl) {
      breadcrumbEl.textContent = produto.name;
    }

    // Monta URL da imagem
    const getImageUrl = (img) => {
      if (!img) return "images/placeholder.png";
      if (img.startsWith("http") || img.startsWith("//")) return img;
      if (img.startsWith("/")) return API_BASE + img;
      return img;
    };

    const images = produto.images || [];
    const mainImage = produto.main_image || images[0] || "images/placeholder.png";
    const imageUrl = getImageUrl(mainImage);

    // Galeria de imagens
    let galleryHtml = "";
    const imgs = images.length > 0 ? images : [mainImage];
    imgs.forEach((img) => {
      const url = getImageUrl(img);
      galleryHtml += `
        <div class="item-slick3" data-thumb="${url}">
          <div class="wrap-pic-w pos-relative">
            <img src="${url}" alt="${produto.name}" />
            <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04" href="${url}">
              <i class="fa fa-expand"></i>
            </a>
          </div>
        </div>
      `;
    });

    // Tamanhos
    const sizes = produto.sizes || [];
    const sizesFiltered = sizes.filter((s) => s && (typeof s === "string" ? s.trim() : s.size));
    let sizesOptions = '<option value="">Selecione o tamanho</option>';
    (sizesFiltered.length ? sizesFiltered : ["P", "M", "G", "GG"]).forEach((s) => {
      const val = typeof s === "object" ? s.size : s;
      if (val) sizesOptions += `<option value="${val}">${val}</option>`;
    });

    // Cores
    const colors = produto.colors || [];
    const colorsFiltered = colors.filter((c) => c && (typeof c === "string" ? c.trim() : c.color));
    let colorsOptions = '<option value="">Selecione a cor</option>';
    (colorsFiltered.length ? colorsFiltered : ["Único"]).forEach((c) => {
      const val = typeof c === "object" ? c.color : c;
      if (val) colorsOptions += `<option value="${val}">${val}</option>`;
    });

    const preco = parseFloat(produto.price) || 0;
    const comparePrice = produto.compare_price ? parseFloat(produto.compare_price) : null;
    const temDesconto = comparePrice && comparePrice > preco;

    const descricao = produto.short_description || produto.description || "";
    const descricaoHtml = produto.description
      ? `<div class="p-t-33 stext-102 cl3">${produto.description}</div>`
      : descricao
        ? `<p class="stext-102 cl3 p-t-23">${descricao}</p>`
        : "";

    const html = `
      <div class="row">
        <div class="col-md-6 col-lg-7 p-b-30">
          <div class="p-l-25 p-r-30 p-lr-0-lg">
            <div class="wrap-slick3 flex-sb flex-w">
              <div class="wrap-slick3-dots"></div>
              <div class="wrap-slick3-arrows flex-sb-m flex-w"></div>
              <div class="slick3 gallery-lb">
                ${galleryHtml}
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-5 p-b-30">
          <div class="p-r-50 p-t-5 p-lr-0-lg">
            <h4 class="mtext-105 cl2 js-name-detail p-b-14">${produto.name}</h4>

            <div class="m-b-15">
              <span class="mtext-106 cl2">R$ ${preco.toFixed(2)}</span>
              ${temDesconto ? `<span class="stext-107 cl6 p-l-10"><del>R$ ${comparePrice.toFixed(2)}</del></span>` : ""}
            </div>

            ${descricaoHtml}

            <div class="p-t-33">
              ${sizesFiltered.length || true ? `
              <div class="flex-w flex-r-m p-b-10">
                <div class="size-203 flex-c-m respon6">Tamanho</div>
                <div class="size-204 respon6-next">
                  <div class="rs1-select2 bor8 bg0">
                    <select class="js-select2 js-select-size" name="size">
                      ${sizesOptions}
                    </select>
                    <div class="dropDownSelect2"></div>
                  </div>
                </div>
              </div>
              ` : ""}

              ${colorsFiltered.length || true ? `
              <div class="flex-w flex-r-m p-b-10">
                <div class="size-203 flex-c-m respon6">Cor</div>
                <div class="size-204 respon6-next">
                  <div class="rs1-select2 bor8 bg0">
                    <select class="js-select2 js-select-color" name="color">
                      ${colorsOptions}
                    </select>
                    <div class="dropDownSelect2"></div>
                  </div>
                </div>
              </div>
              ` : ""}

              <div class="flex-w flex-r-m p-b-10">
                <div class="size-204 flex-w flex-m respon6-next">
                  <div class="wrap-num-product flex-w m-r-20 m-tb-10">
                    <div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m">
                      <i class="fs-16 zmdi zmdi-minus"></i>
                    </div>
                    <input class="mtext-104 cl3 txt-center num-product" type="number" name="num-product" value="1" min="1" max="${produto.quantity || 99}" />
                    <div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
                      <i class="fs-16 zmdi zmdi-plus"></i>
                    </div>
                  </div>
                  <button type="button" class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04 js-addcart-detail">
                    Adicionar ao Carrinho
                  </button>
                </div>
              </div>
            </div>

            <!-- Favoritos - implementar futuramente
            <div class="flex-w flex-m p-l-100 p-t-40 respon7">
              <div class="flex-m bor9 p-r-10 m-r-11">
                <a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 js-addwish-detail tooltip100" data-tooltip="Adicionar aos Favoritos">
                  <i class="zmdi zmdi-favorite"></i>
                </a>
              </div>
            </div>
            -->
          </div>
        </div>
      </div>
    `;

    loadingEl.style.display = "none";
    contentEl.innerHTML = html;
    contentEl.style.display = "block";

    // Select2
    if (typeof $ !== "undefined") {
      $(".js-select2").each(function () {
        $(this).select2({
          minimumResultsForSearch: 20,
          dropdownParent: $(this).next(".dropDownSelect2"),
        });
      });
    }

    // Slick gallery
    if (typeof $ !== "undefined" && $.fn.slick) {
      $(".wrap-slick3").each(function () {
        $(this)
          .find(".slick3")
          .slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            fade: true,
            infinite: true,
            autoplay: false,
            arrows: true,
            appendArrows: $(this).find(".wrap-slick3-arrows"),
            prevArrow:
              '<button class="arrow-slick3 prev-slick3"><i class="fa fa-angle-left" aria-hidden="true"></i></button>',
            nextArrow:
              '<button class="arrow-slick3 next-slick3"><i class="fa fa-angle-right" aria-hidden="true"></i></button>',
            dots: true,
            appendDots: $(this).find(".wrap-slick3-dots"),
            dotsClass: "slick3-dots",
            customPaging: function (slick, index) {
              var portrait = $(slick.$slides[index]).data("thumb");
              return '<img src="' + portrait + '"/><div class="slick3-dot-overlay"></div>';
            },
          });
      });
    }

    // Magnific Popup
    if (typeof $ !== "undefined" && $.fn.magnificPopup) {
      $(".gallery-lb").each(function () {
        $(this).magnificPopup({
          delegate: "a",
          type: "image",
          gallery: { enabled: true },
          mainClass: "mfp-fade",
        });
      });
    }

    // Quantidade +/-
    const numInput = contentEl.querySelector(".num-product");
    const btnDown = contentEl.querySelector(".btn-num-product-down");
    const btnUp = contentEl.querySelector(".btn-num-product-up");
    if (numInput && btnDown && btnUp) {
      btnDown.addEventListener("click", () => {
        let v = parseInt(numInput.value, 10) || 1;
        if (v > 1) numInput.value = v - 1;
      });
      btnUp.addEventListener("click", () => {
        let v = parseInt(numInput.value, 10) || 1;
        const max = parseInt(numInput.max, 10) || 99;
        if (v < max) numInput.value = v + 1;
      });
    }

    // Adicionar ao carrinho
    const addBtn = contentEl.querySelector(".js-addcart-detail");
    if (addBtn && window.LabellaCart) {
      addBtn.addEventListener("click", (e) => {
        e.preventDefault();
        const qty = parseInt(numInput?.value, 10) || 1;
        const prod = {
          id: produto.id,
          name: produto.name,
          price: produto.price,
          main_image: produto.main_image || images[0],
          images: produto.images,
        };
        for (let i = 0; i < qty; i++) {
          window.LabellaCart.addItem(prod);
        }
        if (typeof swal !== "undefined") {
          swal(produto.name, "Adicionado ao carrinho!", "success");
        } else {
          alert("Adicionado ao carrinho!");
        }
      });
    }

    // Favoritos - implementar futuramente
    // const wishBtn = contentEl.querySelector(".js-addwish-detail");
    // if (wishBtn) {
    //   wishBtn.addEventListener("click", (e) => {
    //     e.preventDefault();
    //     if (typeof swal !== "undefined") {
    //       swal(produto.name, "Adicionado aos favoritos!", "success");
    //     }
    //   });
    // }
  } catch (err) {
    console.error("Erro ao carregar produto:", err);
    loadingEl.style.display = "none";
    errorEl.style.display = "block";
  }
});
