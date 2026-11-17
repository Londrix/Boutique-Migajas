<?php
/**
 * ============================================================
 * BOUTIQUE MIGAJAS — Pie de Página (PHP + Tailwind CDN)
 * Archivo: includes/footer.php
 * ============================================================
 */
?>
  </main>

  <!-- ============================================================
       MODAL DE DETALLE DE PRODUCTO (#product-modal)
  ============================================================ -->
  <div id="product-modal" class="hidden fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6 bg-black/60 backdrop-blur-sm" role="dialog" aria-modal="true">
    <div class="relative bg-white rounded-xl shadow-2xl max-w-3xl w-full overflow-hidden border border-brown/10 my-8">
      
      <!-- Botón Cerrar -->
      <button
        type="button"
        onclick="window.BoutiqueModal.close()"
        class="absolute top-4 right-4 z-10 p-2 text-brown-light hover:text-brown bg-cream-light/80 hover:bg-cream-hover rounded-full transition-colors"
        aria-label="Cerrar modal"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" /></svg>
      </button>

      <div class="grid grid-cols-1 md:grid-cols-2 items-stretch">
        <!-- Foto ampliada (Contenedor estrictamente confinado a su columna) -->
        <div class="relative w-full h-72 sm:h-80 md:h-full min-h-[300px] md:min-h-0 bg-cream-hover overflow-hidden flex items-center justify-center min-w-0">
          <img id="modal-img" src="" alt="Prenda" class="w-full h-full object-cover object-center block" />
          <div class="absolute top-3 left-3 z-10">
            <span id="modal-sku" class="bg-gold text-white text-[10px] font-bold tracking-widest uppercase px-3 py-1 rounded-sm shadow-sm">
              SKU
            </span>
          </div>
        </div>

        <!-- Info, Variantes y Stock de Sucursales -->
        <div class="p-6 md:p-8 flex flex-col justify-between max-h-[85vh] overflow-y-auto min-w-0">
          <div>
            <span class="text-xs uppercase tracking-widest text-gold font-semibold">Boutique Migajas</span>
            <h2 id="modal-nombre" class="font-serif text-2xl font-bold text-brown mt-1">Nombre Prenda</h2>
            <p id="modal-precio" class="text-2xl font-bold text-brown mt-2">$0.00 MXN</p>

            <!-- Tallas -->
            <div class="mt-5">
              <label class="block text-xs font-semibold text-brown-light tracking-wider uppercase mb-2">
                Selecciona tu Talla:
              </label>
              <div id="modal-tallas" class="flex flex-wrap gap-2"></div>
            </div>

            <!-- Colores -->
            <div class="mt-4">
              <label class="block text-xs font-semibold text-brown-light tracking-wider uppercase mb-2">
                Color:
              </label>
              <div id="modal-colores" class="flex flex-wrap gap-2"></div>
            </div>

            <!-- Desglose de Stock por Sucursal (Federado) -->
            <div class="mt-6 pt-4 border-t border-brown/10">
              <h4 class="text-xs font-bold uppercase tracking-wider text-brown flex items-center gap-1.5 mb-2.5">
                <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" strokeWidth="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" strokeWidth="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Disponibilidad por Sucursal (Red Federada)
              </h4>
              <div id="modal-stock-sucursales" class="space-y-1.5 bg-cream/60 p-3 rounded-lg border border-brown/5"></div>
            </div>

            <!-- Selector de Cantidad -->
            <div id="modal-qty-container" class="mt-5 flex items-center space-x-3">
              <span class="text-xs font-semibold text-brown-light tracking-wider uppercase">Cantidad:</span>
              <div class="flex items-center border border-brown/20 rounded bg-white">
                <button type="button" onclick="window.BoutiqueModal.changeQty(-1)" class="px-3 py-1 text-brown hover:bg-cream-hover font-bold">-</button>
                <span id="modal-qty-num" class="px-3 py-1 text-xs font-semibold text-brown">1</span>
                <button type="button" onclick="window.BoutiqueModal.changeQty(1)" class="px-3 py-1 text-brown hover:bg-cream-hover font-bold">+</button>
              </div>
            </div>
          </div>

          <!-- Botón de Agregar a la Bolsa -->
          <div class="mt-6 pt-4 border-t border-brown/10">
            <button
              type="button"
              id="modal-btn-add"
              onclick="window.BoutiqueModal.submitAdd()"
              class="w-full py-3.5 px-6 rounded text-xs font-bold tracking-widest uppercase bg-gold hover:bg-gold-dark text-white transition-all shadow-md"
            >
              Agregar a la Bolsa
            </button>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- ============================================================
       FOOTER OSCURO (#121212)
  ============================================================ -->
  <footer class="bg-dark text-white pt-14 pb-8 border-t border-dark-mid mt-auto" aria-label="Pie de página">
    
    <!-- Newsletter Banner -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12 border-b border-white/10">
      <div class="flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="text-center md:text-left">
          <span class="text-[11px] font-bold tracking-[0.25em] text-gold uppercase">Comunidad Boutique Migajas</span>
          <h3 class="font-serif text-xl sm:text-2xl font-semibold text-white mt-1">
            Recibe lo último en moda, estilo &amp; promociones exclusivas
          </h3>
        </div>

        <form onsubmit="event.preventDefault(); alert('¡Gracias por suscribirte al newsletter de Boutique Migajas!'); this.reset();" class="flex w-full md:w-auto max-w-md">
          <input
            type="email"
            placeholder="tucorreo@ejemplo.com"
            required
            class="bg-dark-soft border border-white/15 text-white px-4 py-2.5 text-xs rounded-l-md w-full focus:outline-none focus:border-gold placeholder:text-white/40"
          />
          <button
            type="submit"
            class="bg-gold hover:bg-gold-dark text-white px-6 py-2.5 text-xs font-semibold tracking-wider uppercase rounded-r-md transition-colors whitespace-nowrap"
          >
            Suscribirme
          </button>
        </form>
      </div>
    </div>

    <!-- Grid de 3 Columnas -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 md:grid-cols-3 gap-10">
      
      <!-- Columna 1: Menú -->
      <div>
        <h4 class="font-serif text-sm font-semibold tracking-wider uppercase text-gold pb-2 border-b border-white/10 mb-4">
          Menú de Navegación
        </h4>
        <ul class="space-y-2 text-xs text-white/70">
          <li><a href="index.php" class="hover:text-gold transition-colors">Inicio</a></li>
          <li><a href="index.php?categoria=novedades" class="hover:text-gold transition-colors">Novedades</a></li>
          <li><a href="index.php?categoria=blusas" class="hover:text-gold transition-colors">Blusas</a></li>
          <li><a href="index.php?categoria=vestidos" class="hover:text-gold transition-colors">Vestidos</a></li>
          <li><a href="index.php?categoria=conjuntos" class="hover:text-gold transition-colors">Conjuntos</a></li>
          <li><a href="index.php?categoria=pantalones" class="hover:text-gold transition-colors">Pantalones / Faldas</a></li>
          <li><a href="nosotros.php" class="hover:text-gold transition-colors">Nosotros</a></li>
          <li><a href="checkout.php" class="hover:text-gold transition-colors">Checkout Express</a></li>
        </ul>
      </div>

      <!-- Columna 2: Sucursales de la Red Federada -->
      <div>
        <h4 class="font-serif text-sm font-semibold tracking-wider uppercase text-gold pb-2 border-b border-white/10 mb-4">
          Nuestras Sucursales (Red Federada)
        </h4>
        <ul class="space-y-3.5 text-xs text-white/70">
          <li class="flex items-start gap-2.5">
            <svg class="w-4 h-4 text-gold shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" strokeWidth="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" strokeWidth="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <div>
              <strong class="text-white block font-medium">Sucursal Lerdo</strong>
              <span class="text-[11px] text-white/50">Av. Francisco I. Madero #100, Centro, Lerdo, Dgo.</span>
            </div>
          </li>
          <li class="flex items-start gap-2.5">
            <svg class="w-4 h-4 text-gold shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" strokeWidth="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" strokeWidth="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <div>
              <strong class="text-white block font-medium">Sucursal Gómez Palacio</strong>
              <span class="text-[11px] text-white/50">Paseo Gómez Palacio, Zona Comercial, Dgo.</span>
            </div>
          </li>
          <li class="flex items-start gap-2.5">
            <svg class="w-4 h-4 text-gold shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" strokeWidth="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" strokeWidth="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <div>
              <strong class="text-white block font-medium">Sucursal Torreón</strong>
              <span class="text-[11px] text-white/50">Boulevard Independencia, Centro Histórico, Coah.</span>
            </div>
          </li>
        </ul>
      </div>

      <!-- Columna 3: Contacto & Redes -->
      <div>
        <h4 class="font-serif text-sm font-semibold tracking-wider uppercase text-gold pb-2 border-b border-white/10 mb-4">
          Contacto &amp; WhatsApp
        </h4>
        <ul class="space-y-2.5 text-xs text-white/70">
          <li class="flex items-center gap-2">
            <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" strokeWidth="1.6" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <a href="mailto:contacto@boutiquemigajas.com" class="hover:text-gold transition-colors">
              contacto@boutiquemigajas.com
            </a>
          </li>
          <li class="flex items-center gap-2">
            <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" strokeWidth="1.6" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            <a href="tel:+528712195980" class="hover:text-gold transition-colors">
              +52 (871) 123-4567
            </a>
          </li>
          <li class="flex items-center gap-2">
            <svg class="w-4 h-4 text-gold fill-current" viewBox="0 0 24 24">
              <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.59-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
            </svg>
            <a href="https://wa.me/528711234567" target="_blank" rel="noopener noreferrer" class="hover:text-gold text-emerald-400 font-medium">
              WhatsApp Oficial Migajas
            </a>
          </li>
        </ul>

        <!-- Redes Sociales -->
        <div class="flex gap-3 mt-5">
          <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center hover:bg-gold transition-colors" aria-label="Facebook">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
          </a>
          <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center hover:bg-gold transition-colors" aria-label="Instagram">
            <svg class="w-4 h-4 fill-none stroke-current stroke-2" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
          </a>
        </div>
      </div>

    </div>

    <!-- Copyright Inferior -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between text-[11px] text-white/40 gap-3">
      <p>&copy; <?= date('Y') ?> Boutique Migajas · Todos los derechos reservados.</p>
      <p class="tracking-wide">Hecho con mucho amorcito migajero en Lerdo, Durango</p>
    </div>

  </footer>

  <!-- Script global del carrito y modal -->
  <script src="assets/js/cart.js"></script>
</body>
</html>
