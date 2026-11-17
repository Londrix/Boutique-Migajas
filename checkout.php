<?php
/**
 * ============================================================
 * BOUTIQUE MIGAJAS — Checkout Express (Compra como Invitado)
 * Archivo: checkout.php
 * ============================================================
 */

require_once __DIR__ . '/config/db.php';
$pdo = getDBConnection();
$listaSucursales = obtenerListaSucursales($pdo);

$pageTitle = 'Checkout Express · Boutique Migajas';
$activeCategory = '';
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  
  <!-- Encabezado y Navegación de Regreso -->
  <div class="mb-8">
    <a href="index.php" class="inline-flex items-center text-xs text-brown-light hover:text-gold transition-colors mb-2 font-medium">
      ← Volver al Catálogo
    </a>
    <span class="text-xs uppercase tracking-widest text-gold font-semibold block">Proceso Rápido y Seguro</span>
    <h1 class="font-serif text-3xl font-bold text-brown">
      Realiza tu compra
    </h1>
    
  </div>

  <!-- Estado vacío (Si no hay prendas en el carrito de localStorage) -->
  <div id="checkout-empty-state" class="hidden text-center py-16 bg-white rounded-xl border border-dashed border-brown/20 my-6">
    <svg class="w-16 h-16 text-brown/20 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path d="M6 2 3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
      <line x1="3" y1="6" x2="21" y2="6" />
      <path d="M16 10a4 4 0 01-8 0" />
    </svg>
    <h3 class="font-serif text-xl font-bold text-brown">Tu bolsa de compras está vacía</h3>
    <p class="text-xs text-brown-light mt-1 max-w-sm mx-auto">
      Agrega tus prendas favoritas desde el catálogo para continuar con el checkout.
    </p>
    <a href="index.php" class="mt-6 inline-block bg-gold hover:bg-gold-dark text-white px-6 py-2.5 rounded text-xs font-bold tracking-widest uppercase transition-colors">
      Explorar Catálogo
    </a>
  </div>

  <!-- Contenedor Principal del Checkout -->
  <div id="checkout-content" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <!-- Columna Izquierda: Formulario del Cliente (7 columnas) -->
    <div class="lg:col-span-7 bg-white p-6 sm:p-8 rounded-xl border border-brown/10 shadow-sm">
      <h2 class="font-serif text-lg font-bold text-brown pb-3 border-b border-brown/10 mb-6 flex items-center gap-2">
        <span>1. Datos de Entrega</span>
       
      </h2>

      <!-- Alerta de Error Dinámico -->
      <div id="checkout-error-box" class="hidden mb-6 p-4 bg-red-50 border border-red-200 text-red-700 text-xs rounded-md flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
        </svg>
        <span id="checkout-error-msg"></span>
      </div>

      <form id="form-checkout" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-brown-light mb-1">
            Nombre Completo del Cliente *
          </label>
          <input
            type="text"
            id="input-nombre"
            name="nombre_cliente"
            required
            placeholder="Ej. Mariana González Morales"
            class="w-full bg-cream/30 border border-brown/20 rounded-md px-3.5 py-2.5 text-xs text-brown focus:outline-none focus:border-gold focus:bg-white"
          />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-brown-light mb-1">
              Correo Electrónico *
            </label>
            <input
              type="email"
              id="input-email"
              name="email_contacto"
              required
              placeholder="mariana@ejemplo.com"
              class="w-full bg-cream/30 border border-brown/20 rounded-md px-3.5 py-2.5 text-xs text-brown focus:outline-none focus:border-gold focus:bg-white"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-brown-light mb-1">
              Teléfono de Contacto (WhatsApp) *
            </label>
            <input
              type="tel"
              id="input-telefono"
              name="telefono"
              required
              placeholder="871 123 4567"
              class="w-full bg-cream/30 border border-brown/20 rounded-md px-3.5 py-2.5 text-xs text-brown focus:outline-none focus:border-gold focus:bg-white"
            />
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-brown-light mb-1">
            Dirección Completa de Envío *
          </label>
          <textarea
            id="input-direccion"
            name="direccion_envio"
            required
            rows="3"
            placeholder="Calle, número exterior/interior, colonia, código postal y referencias (Lerdo, Gómez Palacio o Torreón)"
            class="w-full bg-cream/30 border border-brown/20 rounded-md px-3.5 py-2.5 text-xs text-brown focus:outline-none focus:border-gold focus:bg-white"
          ></textarea>
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-brown-light mb-1">
            Sucursal Preferente de Despacho 
          </label>
          <select
            id="input-sucursal"
            name="id_sucursal_despacho"
            class="w-full bg-cream/30 border border-brown/20 rounded-md px-3.5 py-2.5 text-xs text-brown focus:outline-none focus:border-gold focus:bg-white"
          >
            <?php 
                $sucMap = [
                    'Sucursal A (Lerdo)' => 'Sucursal Lerdo',
                    'Sucursal B (Gómez Palacio)' => 'Sucursal Gómez Palacio',
                    'Sucursal C (Torreón)' => 'Sucursal Torreón',
                    'Sucursal A' => 'Sucursal Lerdo',
                    'Sucursal B' => 'Sucursal Gómez Palacio',
                    'Sucursal C' => 'Sucursal Torreón',
                ];
                foreach ($listaSucursales as $sucOpt):
                    $display = $sucMap[$sucOpt] ?? $sucOpt;
            ?>
                <option value="<?= htmlspecialchars($display) ?>">
                    <?= htmlspecialchars($display) ?>
                </option>
            <?php endforeach; ?>
          </select>
        
        </div>

        <div class="pt-5 border-t border-brown/10">
          <button
            type="submit"
            id="btn-confirmar-pedido"
            class="w-full py-4 px-6 rounded-md text-xs font-bold tracking-widest uppercase transition-all shadow-md bg-gold hover:bg-gold-dark text-white hover:shadow-lg flex items-center justify-center gap-2"
          >
            <span id="btn-confirmar-text">Confirmar Pedido</span>
            <svg id="btn-confirmar-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" strokeWidth="2" d="M5 13l4 4L19 7" />
            </svg>
          </button>
        </div>
      </form>
    </div>

    <!-- Columna Derecha: Resumen de la Orden (5 columnas) -->
    <div class="lg:col-span-5 bg-cream-light p-6 rounded-xl border border-brown/10 shadow-sm sticky top-28">
      <h2 class="font-serif text-lg font-bold text-brown pb-3 border-b border-brown/10 mb-4 flex justify-between items-center">
        <span>Resumen de tu Orden</span>
        <span id="checkout-count-badge" class="text-xs font-sans font-medium text-gold">0 prendas</span>
      </h2>

      <!-- Lista de prendas a comprar -->
      <div id="checkout-items-list" class="max-h-72 overflow-y-auto space-y-3 pr-1 mb-4"></div>

      <!-- Desglose de Totales -->
      <div class="space-y-2 text-xs pt-3 border-t border-brown/10">
        <div class="flex justify-between text-brown-light">
          <span>Subtotal:</span>
          <span id="checkout-subtotal">$0.00 MXN</span>
        </div>
        <div class="flex justify-between text-brown-light">
          <span>Envío:</span>
          <span id="checkout-envio" class="text-emerald-700 font-semibold">$0.00 MXN</span>
        </div>
        <div class="flex justify-between text-base font-bold text-brown pt-2 border-t border-brown/10">
          <span>Total a Pagar:</span>
          <span id="checkout-total" class="text-gold font-serif text-xl">$0.00 MXN</span>
        </div>
      </div>


    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const formatPrice = (amount) => {
    return new Intl.NumberFormat('es-MX', {
      style: 'currency',
      currency: 'MXN',
      minimumFractionDigits: 0,
    }).format(amount || 0);
  };

  const renderCheckoutView = () => {
    const cart = window.BoutiqueCart.getCart();
    const content = document.getElementById('checkout-content');
    const emptyState = document.getElementById('checkout-empty-state');
    const list = document.getElementById('checkout-items-list');

    if (cart.length === 0) {
      if (content) content.classList.add('hidden');
      if (emptyState) emptyState.classList.remove('hidden');
      return;
    }

    if (content) content.classList.remove('hidden');
    if (emptyState) emptyState.classList.add('hidden');

    const subtotal = window.BoutiqueCart.getTotalPrice();
    const totalItems = window.BoutiqueCart.getTotalItems();
    const envio = subtotal >= 1200 ? 0 : 150;
    const granTotal = subtotal + envio;

    document.getElementById('checkout-count-badge').textContent = `${totalItems} ${totalItems === 1 ? 'prenda' : 'prendas'}`;
    document.getElementById('checkout-subtotal').textContent = formatPrice(subtotal);
    document.getElementById('checkout-envio').textContent = envio === 0 ? '¡GRATIS!' : formatPrice(envio);
    document.getElementById('checkout-total').textContent = formatPrice(granTotal);
    document.getElementById('btn-confirmar-text').textContent = `Confirmar Pedido · ${formatPrice(granTotal)}`;

    list.innerHTML = cart.map(it => `
      <div class="flex gap-3 text-xs pb-3 border-b border-brown/5">
        <img src="${it.imagen_url}" alt="${it.nombre}" class="w-12 h-16 object-cover rounded bg-cream-hover shrink-0" onerror="this.src='assets/images/productos/blusa.jpg'" />
        <div class="flex-1 flex flex-col justify-between">
          <div>
            <h4 class="font-semibold text-brown line-clamp-1">${it.nombre}</h4>
            <span class="text-[11px] text-brown-muted">Talla: ${it.talla} · Color: ${it.color}</span>
            <span class="text-[10px] text-brown-light block uppercase font-mono">SKU: ${it.sku_producto}</span>
          </div>
          <div class="flex justify-between items-baseline pt-1">
            <span class="text-brown-light">${it.cantidad} × ${formatPrice(it.precio_unitario)}</span>
            <strong class="text-brown">${formatPrice(it.cantidad * it.precio_unitario)}</strong>
          </div>
        </div>
      </div>
    `).join('');
  };

  renderCheckoutView();
  window.addEventListener('bm:cart:updated', renderCheckoutView);

  // Manejador del Submit
  const form = document.getElementById('form-checkout');
  const btn = document.getElementById('btn-confirmar-pedido');
  const errorBox = document.getElementById('checkout-error-box');
  const errorMsg = document.getElementById('checkout-error-msg');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    errorBox.classList.add('hidden');

    const cart = window.BoutiqueCart.getCart();
    if (cart.length === 0) {
      errorMsg.textContent = 'Tu bolsa de compras está vacía.';
      errorBox.classList.remove('hidden');
      return;
    }

    const payload = {
      nombre_cliente: document.getElementById('input-nombre').value.trim(),
      email_contacto: document.getElementById('input-email').value.trim(),
      telefono: document.getElementById('input-telefono').value.trim(),
      direccion_envio: document.getElementById('input-direccion').value.trim(),
      id_sucursal_despacho: document.getElementById('input-sucursal').value,
      items: cart.map(it => ({
        sku_producto: it.sku_producto,
        talla: it.talla,
        color: it.color,
        cantidad: it.cantidad,
        precio_unitario: it.precio_unitario
      }))
    };

    btn.disabled = true;
    btn.innerHTML = `
      <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
      </svg>
      <span>Procesando Transacción SQL...</span>
    `;

    try {
      const response = await fetch('api/procesar_pedido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      const resData = await response.json();

      if (response.ok && resData.status === 'success') {
        // Guardar detalles del ticket para la confirmación
        sessionStorage.setItem('bm_last_order', JSON.stringify(resData.data));
        window.BoutiqueCart.clearCart();
        window.location.href = `confirmacion.php?id=${resData.data.id_pedido}`;
      } else {
        throw new Error(resData.message || 'Error al procesar el pedido');
      }
    } catch (err) {
      console.error('Error al procesar pedido:', err);
      errorMsg.textContent = err.message || 'No se pudo conectar con el servidor central para registrar tu pedido.';
      errorBox.classList.remove('hidden');
      btn.disabled = false;
      renderCheckoutView();
    }
  });
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
