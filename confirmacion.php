<?php
/**
 * ============================================================
 * BOUTIQUE MIGAJAS — Confirmación de Pedido (Ticket de Compra)
 * Archivo: confirmacion.php
 * ============================================================
 */

require_once __DIR__ . '/config/db.php';

$idPedido = (int) ($_GET['id'] ?? 0);
$pdo = getDBConnection();
$pedidoDB = null;
$detallesDB = [];

if ($pdo && $idPedido > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM pedidos_web WHERE id_pedido = ?");
        $stmt->execute([$idPedido]);
        $pedidoDB = $stmt->fetch();

        if ($pedidoDB) {
            $stmtDetalle = $pdo->prepare("SELECT * FROM detalle_pedidos_web WHERE id_pedido = ? ORDER BY id_detalle ASC");
            $stmtDetalle->execute([$idPedido]);
            $detallesDB = $stmtDetalle->fetchAll();
        }
    } catch (Exception $e) {
        error_log('[Confirmación] Error leyendo pedido: ' . $e->getMessage());
    }
}

$pageTitle = 'Pedido Confirmado · Boutique Migajas';
$activeCategory = '';
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-3xl mx-auto px-4 py-16 text-center">
  <!-- Ícono de Éxito -->
  <div class="w-16 h-16 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" strokeWidth="2.5" d="M5 13l4 4L19 7" />
    </svg>
  </div>

  <span class="text-xs font-bold tracking-[0.25em] text-gold uppercase">¡Muchas gracias por tu compra!</span>
  <h1 class="font-serif text-3xl sm:text-4xl font-bold text-brown mt-1">
    Pedido Confirmado <span id="conf-id">#<?= $idPedido > 0 ? $idPedido : '---' ?></span>
  </h1>
  <p class="text-sm text-brown-light mt-2 max-w-lg mx-auto">
    Tu orden ha sido registrada exitosamente en <b>Boutique Migajas</b> bajo la modalidad de Compra Rápida como Invitado.
  </p>

  <!-- Tarjeta del Ticket de Compra -->
  <div class="mt-8 bg-white p-6 sm:p-8 rounded-xl border border-brown/10 text-left shadow-luxury space-y-5">
    
    <!-- Fila Superior: Fecha y Estado -->
    <div class="flex justify-between items-center pb-4 border-b border-brown/10">
      <div>
        <span class="text-xs text-brown-light uppercase tracking-wider block">Fecha y Hora</span>
        <p id="conf-fecha" class="text-sm font-semibold text-brown">
          <?= $pedidoDB ? date('d/m/Y H:i', strtotime($pedidoDB['fecha_pedido'])) : date('d/m/Y H:i') ?>
        </p>
      </div>
      <div class="text-right">
        <span class="text-xs text-brown-light uppercase tracking-wider block">Estado de la Orden</span>
        <p id="conf-estado" class="text-sm font-bold text-emerald-700 uppercase tracking-wider">
          <?= $pedidoDB['estado'] ?? 'PENDIENTE' ?>
        </p>
      </div>
    </div>

    <!-- Fila Media: Datos de Envío y Despacho -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs py-2 border-b border-brown/10">
      <div>
        <strong class="text-brown-light uppercase tracking-wider block mb-1.5">Datos de Envío:</strong>
        <p id="conf-nombre" class="font-semibold text-brown text-sm">
          <?= htmlspecialchars($pedidoDB['nombre_cliente'] ?? 'Cliente Boutique Migajas') ?>
        </p>
        <p id="conf-direccion" class="text-brown-muted mt-0.5">
          <?= htmlspecialchars($pedidoDB['direccion_envio'] ?? 'Dirección registrada') ?>
        </p>
        <p id="conf-telefono" class="text-brown-muted">
          Tel: <?= htmlspecialchars($pedidoDB['telefono'] ?? '---') ?>
        </p>
        <p id="conf-email" class="text-brown-muted">
          <?= htmlspecialchars($pedidoDB['email_contacto'] ?? '---') ?>
        </p>
      </div>
      <div>
        <strong class="text-brown-light uppercase tracking-wider block mb-1.5">Sucursal de Despacho Asignada:</strong>
        <p id="conf-sucursal" class="font-semibold text-gold text-sm">
          <?= htmlspecialchars($pedidoDB['id_sucursal_despacho'] ?? 'Sucursal A (Lerdo)') ?>
        </p>
        <p class="text-[11px] text-brown-muted mt-1 leading-relaxed">
          Tu pedido está siendo preparado por nuestro nodo federado local para entrega o recolección express.
        </p>
      </div>
    </div>

    <!-- Partidas del Pedido -->
    <div>
      <strong class="text-brown-light uppercase tracking-wider text-xs block mb-3">Prendas en el Pedido:</strong>
      <div id="conf-items-list" class="space-y-2.5">
        <?php if (!empty($detallesDB)): ?>
          <?php foreach ($detallesDB as $det): ?>
            <div class="flex justify-between items-center text-xs py-1.5 border-b border-brown/5">
              <div>
                <span class="font-semibold text-brown"><?= htmlspecialchars($det['sku_producto']) ?></span>
                <span class="text-brown-muted block text-[11px]">Talla: <?= htmlspecialchars($det['talla']) ?> · Color: <?= htmlspecialchars($det['color']) ?> · Cantidad: <?= (int)$det['cantidad'] ?></span>
              </div>
              <span class="font-bold text-brown">
                $<?= number_format($det['cantidad'] * $det['precio_unitario'], 2) ?> MXN
              </span>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p id="conf-items-loading" class="text-xs text-brown-muted italic">Cargando desglose de prendas...</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Total -->
    <div class="pt-4 border-t border-brown/10 flex justify-between items-baseline">
      <span class="text-sm font-bold text-brown">Total Pagado:</span>
      <span id="conf-total" class="text-2xl font-bold text-gold font-serif">
        $<?= number_format((float)($pedidoDB['total'] ?? 0), 2) ?> MXN
      </span>
    </div>

  </div>

  <div class="mt-8 flex justify-center gap-4">
    <a
      href="index.php"
      class="bg-gold hover:bg-gold-dark text-white px-8 py-3 rounded text-xs font-bold tracking-widest uppercase transition-colors shadow-md"
    >
      Seguir Comprando
    </a>
  </div>
</div>

<script>
// Sincronización client-side con sessionStorage para modo contingencia o refresco rápido
document.addEventListener('DOMContentLoaded', () => {
  const formatPrice = (val) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(val || 0);

  const rawLastOrder = sessionStorage.getItem('bm_last_order');
  if (rawLastOrder) {
    try {
      const order = JSON.parse(rawLastOrder);
      const urlId = new URLSearchParams(window.location.search).get('id');

      if (!urlId || urlId == order.id_pedido) {
        document.getElementById('conf-id').textContent = `#${order.id_pedido}`;
        if (order.fecha_pedido) {
          document.getElementById('conf-fecha').textContent = new Date(order.fecha_pedido).toLocaleString('es-MX');
        }
        if (order.cliente) {
          document.getElementById('conf-nombre').textContent = order.cliente.nombre_cliente || '';
          document.getElementById('conf-direccion').textContent = order.cliente.direccion_envio || '';
          document.getElementById('conf-telefono').textContent = `Tel: ${order.cliente.telefono || ''}`;
          document.getElementById('conf-email').textContent = order.cliente.email_contacto || '';
        }
        if (order.id_sucursal_despacho) {
          document.getElementById('conf-sucursal').textContent = order.id_sucursal_despacho;
        }
        if (order.total) {
          document.getElementById('conf-total').textContent = formatPrice(order.total);
        }

        // Si la lista de items estaba vacía en el HTML renderizado por PHP
        const list = document.getElementById('conf-items-list');
        const loading = document.getElementById('conf-items-loading');
        if (loading && order.items && order.items.length > 0) {
          list.innerHTML = order.items.map(it => `
            <div class="flex justify-between items-center text-xs py-1.5 border-b border-brown/5">
              <div>
                <span class="font-semibold text-brown">${it.sku_producto}</span>
                <span class="text-brown-muted block text-[11px]">Talla: ${it.talla} · Color: ${it.color} · Cantidad: ${it.cantidad}</span>
              </div>
              <span class="font-bold text-brown">${formatPrice(it.cantidad * it.precio_unitario)}</span>
            </div>
          `).join('');
        }
      }
    } catch (e) {
      console.warn('Error leyendo ticket local:', e);
    }
  }
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
