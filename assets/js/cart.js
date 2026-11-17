/**
 * ============================================================
 * BOUTIQUE MIGAJAS — Carrito y Experiencia de Usuario (JS Vanilla)
 * Archivo: assets/js/cart.js
 * ============================================================
 */

(function () {
  'use strict';

  const STORAGE_KEY = 'boutique_migajas_cart';
  const FALLBACK_IMG = 'assets/images/productos/blusa.jpg';

  // --- Funciones de Acceso al Storage ---
  function getCart() {
    try {
      const data = localStorage.getItem(STORAGE_KEY);
      return data ? JSON.parse(data) : [];
    } catch (e) {
      console.warn('Error leyendo carrito de localStorage:', e);
      return [];
    }
  }

  function saveCart(cart) {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
      updateCartBadges();
      renderCartDrawer();
      // Notificar a otras vistas como checkout.php
      window.dispatchEvent(new CustomEvent('bm:cart:updated', { detail: { cart } }));
    } catch (e) {
      console.warn('Error guardando carrito en localStorage:', e);
    }
  }

  function formatPrice(amount) {
    return new Intl.NumberFormat('es-MX', {
      style: 'currency',
      currency: 'MXN',
      minimumFractionDigits: 0,
    }).format(amount || 0);
  }

  // --- Operaciones del Carrito ---
  window.BoutiqueCart = {
    getCart,

    addToCart(product, options = {}) {
      const {
        talla = 'ÚNICA',
        color = 'Estándar',
        cantidad = 1,
        existenciasMax = 99,
        sucursal = null,
      } = options;

      const cartId = `${product.sku}_${talla}_${color}`.replace(/\s+/g, '_');
      const cart = getCart();
      const existingIndex = cart.findIndex((it) => it.cartId === cartId);

      if (existingIndex > -1) {
        const newQty = Math.min(cart[existingIndex].cantidad + cantidad, existenciasMax);
        cart[existingIndex].cantidad = newQty;
      } else {
        cart.push({
          cartId,
          sku_producto: product.sku,
          nombre: product.nombre,
          precio_unitario: Number(product.precio),
          imagen_url: product.imagen_url || FALLBACK_IMG,
          talla,
          color,
          cantidad: Math.min(cantidad, existenciasMax),
          sucursal_preferida: sucursal,
          existenciasMax,
        });
      }

      saveCart(cart);
      this.openDrawer();
    },

    updateQuantity(cartId, delta) {
      const cart = getCart();
      const updated = cart
        .map((it) => {
          if (it.cartId === cartId) {
            const next = it.cantidad + delta;
            if (next <= 0) return null;
            return { ...it, cantidad: Math.min(next, it.existenciasMax || 99) };
          }
          return it;
        })
        .filter(Boolean);

      saveCart(updated);
    },

    removeFromCart(cartId) {
      const cart = getCart();
      const filtered = cart.filter((it) => it.cartId !== cartId);
      saveCart(filtered);
    },

    clearCart() {
      saveCart([]);
    },

    getTotalItems() {
      return getCart().reduce((acc, curr) => acc + (curr.cantidad || 0), 0);
    },

    getTotalPrice() {
      return getCart().reduce((acc, curr) => acc + (curr.cantidad || 0) * (curr.precio_unitario || 0), 0);
    },

    openDrawer() {
      const drawer = document.getElementById('cart-drawer');
      if (drawer) {
        drawer.classList.remove('hidden');
        renderCartDrawer();
      }
    },

    closeDrawer() {
      const drawer = document.getElementById('cart-drawer');
      if (drawer) {
        drawer.classList.add('hidden');
      }
    },
  };

  // --- Actualización Visual de Badges ---
  function updateCartBadges() {
    const total = window.BoutiqueCart.getTotalItems();
    const badges = document.querySelectorAll('.js-cart-count');

    badges.forEach((b) => {
      if (total > 0) {
        b.textContent = total > 99 ? '99+' : total;
        b.classList.remove('hidden');
      } else {
        b.textContent = '0';
        b.classList.add('hidden');
      }
    });
  }

  // --- Renderizado del Drawer Lateral ---
  function renderCartDrawer() {
    const listContainer = document.getElementById('cart-drawer-items');
    const footerContainer = document.getElementById('cart-drawer-footer');
    const countLabel = document.getElementById('cart-drawer-count');

    if (!listContainer) return;

    const cart = getCart();
    const totalItems = window.BoutiqueCart.getTotalItems();
    const totalPrice = window.BoutiqueCart.getTotalPrice();

    if (countLabel) {
      countLabel.textContent = `${totalItems} ${totalItems === 1 ? 'prenda seleccionada' : 'prendas seleccionadas'}`;
    }

    if (cart.length === 0) {
      listContainer.innerHTML = `
        <div class="h-full flex flex-col items-center justify-center text-center p-8 text-brown-light">
          <svg class="w-16 h-16 text-brown/20 stroke-[1.2] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M6 2 3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
            <line x1="3" y1="6" x2="21" y2="6" />
            <path d="M16 10a4 4 0 01-8 0" />
          </svg>
          <p class="font-serif text-lg font-semibold text-brown mb-1">Tu bolsa está vacía</p>
          <p class="text-xs max-w-xs text-brown-muted">Descubre nuestras prendas de temporada y encuentra el estilo que mejor te acompaña.</p>
          <button onclick="window.BoutiqueCart.closeDrawer()" class="mt-6 bg-gold hover:bg-gold-dark text-white px-6 py-2.5 rounded text-xs font-bold tracking-widest uppercase transition-colors">
            Explorar Catálogo
          </button>
        </div>
      `;
      if (footerContainer) footerContainer.classList.add('hidden');
      return;
    }

    if (footerContainer) footerContainer.classList.remove('hidden');

    const envio = totalPrice >= 1200 ? 0 : 150;
    const granTotal = totalPrice + envio;

    listContainer.innerHTML = cart
      .map(
        (it) => `
        <div class="flex gap-4 p-3 bg-cream/40 rounded-lg border border-brown/5 relative group">
          <img src="${it.imagen_url}" alt="${it.nombre}" class="w-16 h-20 object-cover rounded bg-cream-hover shrink-0" onerror="this.src='${FALLBACK_IMG}'" />
          <div class="flex-1 flex flex-col justify-between">
            <div class="pr-6">
              <h4 class="font-serif text-sm font-semibold text-brown line-clamp-1">${it.nombre}</h4>
              <div class="text-[11px] text-brown-light space-x-2 mt-0.5">
                <span>Talla: <b class="text-brown">${it.talla}</b></span>
                <span>·</span>
                <span>Color: <b class="text-brown">${it.color}</b></span>
              </div>
              <span class="text-[10px] font-mono text-brown-muted uppercase">SKU: ${it.sku_producto}</span>
            </div>
            <div class="flex items-center justify-between mt-2 pt-2 border-t border-brown/5">
              <div class="flex items-center border border-brown/20 rounded bg-white">
                <button onclick="window.BoutiqueCart.updateQuantity('${it.cartId}', -1)" class="px-2 py-0.5 text-xs text-brown hover:bg-cream-hover font-bold">-</button>
                <span class="px-2 py-0.5 text-xs font-semibold text-brown">${it.cantidad}</span>
                <button onclick="window.BoutiqueCart.updateQuantity('${it.cartId}', 1)" class="px-2 py-0.5 text-xs text-brown hover:bg-cream-hover font-bold">+</button>
              </div>
              <span class="text-sm font-bold text-brown">${formatPrice(it.precio_unitario * it.cantidad)}</span>
            </div>
          </div>
          <button onclick="window.BoutiqueCart.removeFromCart('${it.cartId}')" class="absolute top-2 right-2 text-brown-light hover:text-red-600 p-1" title="Eliminar">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
          </button>
        </div>
      `
      )
      .join('');

    // Actualizar Totales del Drawer
    const subtotalEl = document.getElementById('cart-drawer-subtotal');
    const envioEl = document.getElementById('cart-drawer-envio');
    const totalEl = document.getElementById('cart-drawer-total');

    if (subtotalEl) subtotalEl.textContent = formatPrice(totalPrice);
    if (envioEl) envioEl.textContent = envio === 0 ? 'Gratis' : formatPrice(envio);
    if (totalEl) totalEl.textContent = formatPrice(granTotal);
  }

  // --- Modal de Producto (Detalles, Variantes y Stock de Sucursales) ---
  window.BoutiqueModal = {
    currentProduct: null,
    selectedTalla: '',
    selectedColor: '',
    cantidad: 1,

    open(productJson) {
      try {
        const prod = typeof productJson === 'string' ? JSON.parse(productJson) : productJson;
        this.currentProduct = prod;
        this.cantidad = 1;

        const modal = document.getElementById('product-modal');
        if (!modal) return;

        // Foto ampliada
        const img = document.getElementById('modal-img');
        if (img) {
          img.src = prod.imagen_url || FALLBACK_IMG;
          img.alt = prod.nombre;
        }

        // Títulos y SKU
        document.getElementById('modal-sku').textContent = `SKU: ${prod.sku}`;
        document.getElementById('modal-nombre').textContent = prod.nombre;
        document.getElementById('modal-precio').textContent = formatPrice(prod.precio);

        // Extraer tallas y colores únicos
        const tallas = [...new Set(prod.variantes?.map((v) => v.talla) || ['ÚNICA'])];
        const colores = [...new Set(prod.variantes?.map((v) => v.color) || ['Estándar'])];

        this.selectedTalla = tallas[0] || 'ÚNICA';
        this.selectedColor = colores[0] || 'Estándar';

        this.renderTallas(tallas);
        this.renderColores(colores);
        this.updateStockDisplay();

        modal.classList.remove('hidden');
      } catch (e) {
        console.error('Error abriendo modal de producto:', e);
      }
    },

    close() {
      const modal = document.getElementById('product-modal');
      if (modal) modal.classList.add('hidden');
    },

    renderTallas(tallas) {
      const container = document.getElementById('modal-tallas');
      if (!container) return;
      container.innerHTML = tallas
        .map(
          (t) => `
        <button type="button" onclick="window.BoutiqueModal.setTalla('${t}')" class="px-3 py-1.5 text-xs font-semibold rounded border transition-all ${
            t === this.selectedTalla ? 'bg-gold text-white border-gold shadow-sm' : 'bg-white text-brown border-brown/20 hover:border-gold'
          }">
          ${t}
        </button>
      `
        )
        .join('');
    },

    renderColores(colores) {
      const container = document.getElementById('modal-colores');
      if (!container) return;
      container.innerHTML = colores
        .map(
          (c) => `
        <button type="button" onclick="window.BoutiqueModal.setColor('${c}')" class="px-3 py-1.5 text-xs font-medium rounded border transition-all ${
            c === this.selectedColor ? 'bg-brown text-white border-brown shadow-sm' : 'bg-white text-brown border-brown/20 hover:border-brown'
          }">
          ${c}
        </button>
      `
        )
        .join('');
    },

    setTalla(t) {
      this.selectedTalla = t;
      const tallas = [...new Set(this.currentProduct.variantes?.map((v) => v.talla) || ['ÚNICA'])];
      this.renderTallas(tallas);
      this.updateStockDisplay();
    },

    setColor(c) {
      this.selectedColor = c;
      const colores = [...new Set(this.currentProduct.variantes?.map((v) => v.color) || ['Estándar'])];
      this.renderColores(colores);
      this.updateStockDisplay();
    },

    updateStockDisplay() {
      const stockContainer = document.getElementById('modal-stock-sucursales');
      const addBtn = document.getElementById('modal-btn-add');
      const qtyContainer = document.getElementById('modal-qty-container');
      const qtyNum = document.getElementById('modal-qty-num');

      const variante = this.currentProduct.variantes?.find(
        (v) => v.talla === this.selectedTalla && v.color === this.selectedColor
      ) || this.currentProduct.variantes?.[0];

      const stockActual = variante ? variante.existencias_totales : this.currentProduct.total_existencias;
      const isOut = stockActual <= 0;

      if (qtyNum) qtyNum.textContent = this.cantidad;

      // Renderizar el desglose de sucursales
      if (stockContainer) {
        const sucursales = variante?.disponibilidad_sucursales || this.currentProduct.disponibilidad_por_sucursal || [];
        if (sucursales.length > 0) {
          stockContainer.innerHTML = sucursales
            .map(
              (s) => `
            <div class="flex justify-between items-center text-xs">
            <span class="text-brown font-medium">${s.sucursal}</span>

              ${
                s.existencias > 0
                  ? `<span class="text-emerald-700 font-semibold bg-emerald-100/80 px-2 py-0.5 rounded text-[11px]">${s.existencias} disponibles</span>`
                  : `<span class="text-red-600 font-medium bg-red-50 px-2 py-0.5 rounded text-[11px]">Agotado</span>`
              }
            </div>
          `
            )
            .join('');
        } else {
          stockContainer.innerHTML = '<p class="text-xs text-brown-light">Stock disponible en tienda central.</p>';
        }
      }

      // Estado del botón y cantidad
      if (addBtn) {
        if (isOut) {
          addBtn.disabled = true;
          addBtn.textContent = 'Variante Agotada';
          addBtn.className = 'w-full py-3.5 px-6 rounded text-xs font-bold tracking-widest uppercase bg-brown/20 text-brown-light cursor-not-allowed';
          if (qtyContainer) qtyContainer.classList.add('opacity-40', 'pointer-events-none');
        } else {
          addBtn.disabled = false;
          addBtn.textContent = 'Agregar a la Bolsa';
          addBtn.className = 'w-full py-3.5 px-6 rounded text-xs font-bold tracking-widest uppercase bg-gold hover:bg-gold-dark text-white transition-all shadow-md';
          if (qtyContainer) qtyContainer.classList.remove('opacity-40', 'pointer-events-none');
        }
      }
    },

    changeQty(delta) {
      const variante = this.currentProduct.variantes?.find(
        (v) => v.talla === this.selectedTalla && v.color === this.selectedColor
      ) || this.currentProduct.variantes?.[0];
      const max = variante ? variante.existencias_totales : this.currentProduct.total_existencias;
      this.cantidad = Math.max(1, Math.min(this.cantidad + delta, max || 99));
      const qtyNum = document.getElementById('modal-qty-num');
      if (qtyNum) qtyNum.textContent = this.cantidad;
    },

    submitAdd() {
      if (!this.currentProduct) return;
      const variante = this.currentProduct.variantes?.find(
        (v) => v.talla === this.selectedTalla && v.color === this.selectedColor
      ) || this.currentProduct.variantes?.[0];

      window.BoutiqueCart.addToCart(this.currentProduct, {
        talla: this.selectedTalla,
        color: this.selectedColor,
        cantidad: this.cantidad,
        existenciasMax: variante ? variante.existencias_totales : 99,
      });

      this.close();
    },
  };

  // --- Inicialización al cargar el DOM ---
  document.addEventListener('DOMContentLoaded', () => {
    updateCartBadges();

    // Cerrar modales con tecla Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        window.BoutiqueCart.closeDrawer();
        window.BoutiqueModal.close();
      }
    });

    // Control de barra de búsqueda desplegable
    const btnSearch = document.getElementById('btn-toggle-search');
    const searchBar = document.getElementById('search-dropdown-bar');
    if (btnSearch && searchBar) {
      btnSearch.addEventListener('click', () => {
        searchBar.classList.toggle('hidden');
        const input = searchBar.querySelector('input');
        if (input && !searchBar.classList.contains('hidden')) {
          setTimeout(() => input.focus(), 100);
        }
      });
    }

    // Menú móvil hamburguesa
    const btnMobile = document.getElementById('btn-mobile-menu');
    const mobileMenu = document.getElementById('mobile-nav-menu');
    if (btnMobile && mobileMenu) {
      btnMobile.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
      });
    }
  });
})();
