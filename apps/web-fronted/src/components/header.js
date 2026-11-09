/**
 * ============================================================
 * BOUTIQUE MIGAJAS — Lógica del Header
 * Archivo: src/components/header.js
 * ============================================================
 */

(function () {
  'use strict';

  /* ── Elementos ─────────────────────────────────────────── */
  const btnSearch    = document.getElementById('btn-search');
  const searchBar    = document.getElementById('search-bar');
  const searchInput  = document.getElementById('search-input');
  const searchForm   = document.getElementById('search-form');
  const btnCart      = document.getElementById('btn-cart');
  const cartCount    = document.getElementById('cart-count');
  const btnHamburger = document.getElementById('btn-hamburger');
  const navList      = document.getElementById('nav-list');

  /* ── Estado del carrito (preparado para backend) ──────── */
  /**
   * TODO: Reemplazar con llamada real a la API:
   *   GET /api/cart  → { items: [...], total: N }
   *   POST /api/cart → agregar producto
   * Ver: src/services/cartService.js
   */
  let cartState = {
    items: [],   // [{ productId, variantId, qty, branchId }]
    total: 0,
  };

  function updateCartBadge(count) {
    if (!cartCount) return;
    cartCount.textContent = count > 0 ? (count > 99 ? '99+' : count) : '';
    cartCount.style.display = count > 0 ? 'flex' : 'none';
  }

  // Inicializar badge desde sessionStorage (offline fallback)
  const savedCart = JSON.parse(sessionStorage.getItem('bm_cart') || '{"items":[]}');
  updateCartBadge(savedCart.items?.length || 0);

  /* ── Búsqueda ──────────────────────────────────────────── */
  function toggleSearch() {
    const isOpen = searchBar.classList.toggle('is-open');
    searchBar.setAttribute('aria-hidden', !isOpen);
    btnSearch.setAttribute('aria-expanded', isOpen);
    if (isOpen) {
      setTimeout(() => searchInput?.focus(), 100);
    }
  }

  btnSearch?.addEventListener('click', toggleSearch);

  // Cerrar con Escape
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && searchBar.classList.contains('is-open')) {
      toggleSearch();
      btnSearch?.focus();
    }
  });

  // Submit de búsqueda
  searchForm?.addEventListener('submit', (e) => {
    e.preventDefault();
    const query = searchInput?.value.trim();
    if (!query) return;
    /**
     * TODO: Conectar con API de búsqueda:
     *   GET /api/products/search?q=:query&branch=:branchId
     */
    console.info('[BM] Búsqueda:', query);
    window.location.href = `busqueda.html?q=${encodeURIComponent(query)}`;
  });

  /* ── Carrito ───────────────────────────────────────────── */
  btnCart?.addEventListener('click', () => {
    /**
     * TODO: Abrir panel lateral del carrito o redirigir:
     *   window.location.href = 'carrito.html';
     * o disparar evento personalizado:
     *   document.dispatchEvent(new CustomEvent('bm:cart:open'));
     */
    console.info('[BM] Carrito abierto. Items:', cartState.items.length);
  });

  /* ── Menú hamburguesa (mobile) ─────────────────────────── */
  btnHamburger?.addEventListener('click', () => {
    const isOpen = navList.classList.toggle('is-open');
    btnHamburger.classList.toggle('is-open');
    btnHamburger.setAttribute('aria-expanded', isOpen);
    btnHamburger.setAttribute('aria-label', isOpen ? 'Cerrar menú' : 'Abrir menú');
  });

  /* ── API pública del módulo ─────────────────────────────── */
  window.BoutiqueHeader = {
    /**
     * Actualiza el contador del carrito desde el exterior.
     * Llámalo después de agregar/quitar productos:
     *   BoutiqueHeader.setCartCount(3);
     */
    setCartCount(n) { updateCartBadge(n); },

    /**
     * Marca el enlace activo según la ruta actual.
     * Útil en SPAs o al navegar entre páginas:
     *   BoutiqueHeader.setActiveNav('vestidos');
     */
    setActiveNav(key) {
      navList?.querySelectorAll('.bm-nav__link').forEach((a) => {
        a.classList.remove('bm-nav__link--active');
        a.removeAttribute('aria-current');
        if (a.href.includes(key)) {
          a.classList.add('bm-nav__link--active');
          a.setAttribute('aria-current', 'page');
        }
      });
    },
  };
})();
