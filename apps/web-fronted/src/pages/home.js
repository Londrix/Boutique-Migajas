/**
 * ============================================================
 * BOUTIQUE MIGAJAS — Lógica de la Página Home
 * Archivo: src/pages/home.js
 *
 * Renderiza el grid de productos con datos mock.
 * Para conectar con backend: reemplazar getMockProducts()
 * con getProducts() de productService.js
 * ============================================================
 */

import { getMockProducts /* , getProducts */ } from '../services/productService.js';

/* ── Formato de moneda ──────────────────────────────────── */
const currencyFmt = new Intl.NumberFormat('es-MX', {
  style:    'currency',
  currency: 'MXN',
  minimumFractionDigits: 0,
});
const fmt = (n) => currencyFmt.format(n);

/* ── Render de tarjeta de producto ─────────────────────── */
function renderProductCard(product) {
  const {
    id, name, category, price, salePrice,
    image, imageAlt, badge, stock, stockLabel, branchStock,
  } = product;

  /* Badge */
  const badgeMap = { Nuevo: 'new', Oferta: 'sale', Agotado: 'sold-out' };
  const badgeHtml = badge
    ? `<span class="bm-card__badge bm-card__badge--${badgeMap[badge] ?? 'new'}">${badge}</span>`
    : '';

  /* Stock */
  const stockClass = {
    available: 'bm-card__stock--available',
    low:       'bm-card__stock--low',
    out:       'bm-card__stock--out',
  }[stock] ?? 'bm-card__stock--out';

  /* Precio */
  const priceHtml = salePrice
    ? `<span class="bm-card__price bm-card__price--sale">${fmt(salePrice)}</span>
       <span class="bm-card__price bm-card__price--original">${fmt(price)}</span>`
    : `<span class="bm-card__price">${fmt(price)}</span>`;

  /* Stock por sucursal (si existe) */
  let branchStockHtml = '';
  if (branchStock?.length) {
    const items = branchStock.map(
      (b) => `<li title="${b.branchName}: ${b.stock} pzas.">${b.branchName} · ${b.stock}</li>`
    ).join('');
    branchStockHtml = `
      <div class="bm-card__branch-stock" data-product-id="${id}">
        <span>Stock por sucursal:</span>
        <ul>${items}</ul>
      </div>`;
  }

  /* Botones de acción */
  const isOut = stock === 'out';
  const addBtn = isOut
    ? `<button class="bm-card__action-btn bm-card__action-btn--disabled" disabled>Agotado</button>`
    : `<button class="bm-card__action-btn bm-card__action-btn--primary js-add-cart"
              data-product-id="${id}">Agregar</button>`;

  return `
    <article class="bm-card" data-product-id="${id}" role="article">
      ${badgeHtml}
      <div class="bm-card__img-wrap">
        <img
          class="bm-card__img"
          src="${image}"
          alt="${imageAlt}"
          loading="lazy"
          width="600"
          height="800"
        />
        <!-- Quick actions (visible al hover) -->
        <div class="bm-card__actions" aria-label="Acciones rápidas">
          ${addBtn}
          <a href="producto.html?id=${id}"
             class="bm-card__action-btn bm-card__action-btn--secondary">Ver más</a>
        </div>
      </div>
      <div class="bm-card__body">
        <span class="bm-card__category">${category}</span>
        <h3 class="bm-card__name">
          <a href="producto.html?id=${id}">${name}</a>
        </h3>
        <div class="bm-card__price-row">${priceHtml}</div>
        <span class="bm-card__stock ${stockClass}">${stockLabel}</span>
        ${branchStockHtml}
      </div>
    </article>`;
}

/* ── Skeletons de carga ─────────────────────────────────── */
function renderSkeletons(count = 8) {
  return Array.from({ length: count }, () => `
    <div class="bm-card bm-card--skeleton" aria-hidden="true">
      <div class="bm-card__img-wrap"></div>
      <div class="bm-card__body">
        <span class="bm-card__category">&nbsp;</span>
        <h3 class="bm-card__name">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h3>
        <div class="bm-card__price-row">
          <span class="bm-card__price">&nbsp;&nbsp;&nbsp;&nbsp;</span>
        </div>
      </div>
    </div>`).join('');
}

/* ── Inicialización ─────────────────────────────────────── */
async function initHome() {
  const grid       = document.getElementById('product-grid');
  const filterTags = document.querySelectorAll('.bm-products__filter-tag');
  const sortSelect = document.getElementById('sort-select');

  if (!grid) return;

  /* Estado local */
  let allProducts  = [];
  let activeFilter = 'todos';
  let activeSort   = 'newest';

  /* Renderizar */
  function renderGrid(products) {
    if (!products.length) {
      grid.innerHTML = `
        <p class="bm-products__empty" style="grid-column:1/-1; text-align:center; padding:3rem; color:var(--bm-brown-light);">
          No hay productos disponibles en esta categoría.
        </p>`;
      return;
    }
    grid.innerHTML = products.map(renderProductCard).join('');
    bindCartButtons();
  }

  /* Filtrar y ordenar */
  function applyFiltersAndSort() {
    let filtered = activeFilter === 'todos'
      ? allProducts
      : allProducts.filter(
          (p) => p.category.toLowerCase().replace(/\s/g, '-') === activeFilter
        );

    filtered = [...filtered].sort((a, b) => {
      if (activeSort === 'price_asc')  return (a.salePrice ?? a.price) - (b.salePrice ?? b.price);
      if (activeSort === 'price_desc') return (b.salePrice ?? b.price) - (a.salePrice ?? a.price);
      return b.id - a.id; // 'newest'
    });

    renderGrid(filtered);
  }

  /* Cargar datos */
  grid.innerHTML = renderSkeletons(8);

  try {
    /*
     * TODO: Reemplazar con API real cuando el backend esté listo:
     *   const { products } = await getProducts({ page: 1, limit: 16 });
     *   allProducts = products;
     */
    await new Promise((r) => setTimeout(r, 600)); // simula latencia
    allProducts = getMockProducts();
    applyFiltersAndSort();
  } catch (err) {
    console.error('[BM] Error cargando productos:', err);
    grid.innerHTML = `
      <p style="grid-column:1/-1; text-align:center; padding:3rem; color:#C0392B;">
        Error al cargar productos. <button onclick="location.reload()" style="text-decoration:underline;cursor:pointer">Reintentar</button>
      </p>`;
  }

  /* Filtros */
  filterTags.forEach((tag) => {
    tag.addEventListener('click', () => {
      filterTags.forEach((t) => t.classList.remove('bm-products__filter-tag--active'));
      tag.classList.add('bm-products__filter-tag--active');
      activeFilter = tag.dataset.filter ?? 'todos';
      applyFiltersAndSort();
    });
  });

  /* Ordenamiento */
  sortSelect?.addEventListener('change', () => {
    activeSort = sortSelect.value;
    applyFiltersAndSort();
  });

  /* Agregar al carrito */
  function bindCartButtons() {
    document.querySelectorAll('.js-add-cart').forEach((btn) => {
      btn.addEventListener('click', () => {
        const productId = btn.dataset.productId;
        addToCart(productId);
      });
    });
  }

  function addToCart(productId) {
    /*
     * TODO: Conectar con API:
     *   POST /api/cart
     *   Body: { productId, qty: 1, branchId: selectedBranch }
     */
    const cart = JSON.parse(sessionStorage.getItem('bm_cart') || '{"items":[]}');
    const existing = cart.items.find((i) => i.productId == productId);
    if (existing) {
      existing.qty += 1;
    } else {
      cart.items.push({ productId, qty: 1 });
    }
    sessionStorage.setItem('bm_cart', JSON.stringify(cart));

    /* Actualizar badge */
    if (window.BoutiqueHeader) {
      window.BoutiqueHeader.setCartCount(cart.items.length);
    }

    /* Feedback visual rápido */
    const btn = document.querySelector(`.js-add-cart[data-product-id="${productId}"]`);
    if (btn) {
      const original = btn.textContent;
      btn.textContent = '✓ Agregado';
      btn.disabled = true;
      setTimeout(() => {
        btn.textContent = original;
        btn.disabled = false;
      }, 1500);
    }
  }
}

/* Auto-init al cargar el DOM */
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initHome);
} else {
  initHome();
}
