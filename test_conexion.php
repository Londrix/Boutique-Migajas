<?php
/**
 * ============================================================
 * BOUTIQUE MIGAJAS — Diagnóstico de Conexión Docker PostgreSQL
 * Archivo: test_conexion.php
 * Acceso: http://localhost/WWW/7toSemestre/BoutiqueMigajas/test_conexion.php
 * ============================================================
 */

require_once __DIR__ . '/config/db.php';

$extCargada = extension_loaded('pdo_pgsql');
$pdo = null;
$errorConexion = null;
$vistaInventario = null;
$errorVista = null;
$filasInventario = [];
$tablasTransaccionales = [];

if ($extCargada) {
    try {
        $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', DB_HOST, DB_PORT, DB_NAME);
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT            => 3,
        ]);

        // Verificar vista_inventario_global
        try {
            $stmt = $pdo->query("SELECT sucursal, sku, nombre, talla, color, existencias, precio, imagen_url FROM vista_inventario_global LIMIT 10");
            $filasInventario = $stmt->fetchAll();
            $vistaInventario = true;
        } catch (Exception $e) {
            $vistaInventario = false;
            $errorVista = $e->getMessage();
        }

        // Verificar tablas de pedidos
        try {
            $tablas = ['pedidos_web', 'detalle_pedidos_web'];
            foreach ($tablas as $t) {
                $check = $pdo->query("SELECT to_regclass('$t') as existe")->fetch();
                $tablasTransaccionales[$t] = !empty($check['existe']);
            }
        } catch (Exception $e) {
            // Ignorar
        }

    } catch (PDOException $e) {
        $errorConexion = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Diagnóstico de Conexión · Boutique Migajas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
</head>
<body class="bg-slate-50 text-slate-800 font-['Inter'] p-6 sm:p-12">
  <div class="max-w-3xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
    
    <div class="border-b pb-4 mb-6">
      <span class="text-xs uppercase tracking-widest text-amber-600 font-bold">Boutique Migajas</span>
      <h1 class="text-2xl font-bold text-slate-900 mt-1">Diagnóstico de Conexión a PostgreSQL (Docker)</h1>
      <p class="text-xs text-slate-500 mt-1">Verifica la comunicación entre XAMPP (Apache/PHP) y tu contenedor Docker (pgAdmin / db_central).</p>
    </div>

    <div class="space-y-4">
      
      <!-- Paso 1: Extensión PHP -->
      <div class="p-4 rounded-xl border flex items-start justify-between <?= $extCargada ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200' ?>">
        <div>
          <h3 class="text-sm font-bold <?= $extCargada ? 'text-emerald-900' : 'text-red-900' ?>">
            1. Extensión PHP pdo_pgsql
          </h3>
          <p class="text-xs mt-1 <?= $extCargada ? 'text-emerald-700' : 'text-red-700' ?>">
            <?= $extCargada ? '✓ Extensión habilitada y lista para comunicar PHP con PostgreSQL.' : '✗ La extensión pdo_pgsql está desactivada en C:\xampp\php\php.ini' ?>
          </p>
          <?php if (!$extCargada): ?>
            <p class="text-[11px] text-red-600 mt-2 bg-white/70 p-2 rounded">
              <b>Solución:</b> Abre <code>C:\xampp\php\php.ini</code>, quita el punto y coma a <code>;extension=pdo_pgsql</code> y reinicia Apache en XAMPP.
            </p>
          <?php endif; ?>
        </div>
        <span class="text-xs font-bold px-2.5 py-1 rounded-full uppercase <?= $extCargada ? 'bg-emerald-200 text-emerald-800' : 'bg-red-200 text-red-800' ?>">
          <?= $extCargada ? 'Activo' : 'Inactivo' ?>
        </span>
      </div>

      <!-- Paso 2: Conexión con el Contenedor Docker -->
      <div class="p-4 rounded-xl border flex items-start justify-between <?= $pdo ? 'bg-emerald-50 border-emerald-200' : 'bg-amber-50 border-amber-200' ?>">
        <div>
          <h3 class="text-sm font-bold <?= $pdo ? 'text-emerald-900' : 'text-amber-900' ?>">
            2. Conexión a boutique_central_db (Puerto <?= htmlspecialchars(DB_PORT) ?>)
          </h3>
          <p class="text-xs mt-1 <?= $pdo ? 'text-emerald-700' : 'text-amber-800' ?>">
            <?= $pdo ? '✓ Conexión exitosa al contenedor Docker db_central.' : 'Esperando conexión con el contenedor Docker.' ?>
          </p>
          <?php if (!$pdo && $errorConexion): ?>
            <p class="text-[11px] text-red-600 mt-2 bg-white/70 p-2 rounded font-mono">
              Error: <?= htmlspecialchars($errorConexion) ?>
            </p>
            <p class="text-[11px] text-amber-800 mt-2">
              <b>Solución:</b> Asegúrate de haber iniciado Docker Desktop y ejecutado <code>docker compose up -d</code>.
            </p>
          <?php endif; ?>
        </div>
        <span class="text-xs font-bold px-2.5 py-1 rounded-full uppercase <?= $pdo ? 'bg-emerald-200 text-emerald-800' : 'bg-amber-200 text-amber-800' ?>">
          <?= $pdo ? 'Conectado' : 'Sin Conexión' ?>
        </span>
      </div>

      <!-- Paso 3: Vista Federada vista_inventario_global -->
      <div class="p-4 rounded-xl border flex items-start justify-between <?= $vistaInventario ? 'bg-emerald-50 border-emerald-200' : 'bg-slate-50 border-slate-200' ?>">
        <div>
          <h3 class="text-sm font-bold <?= $vistaInventario ? 'text-emerald-900' : 'text-slate-900' ?>">
            3. Vista vista_inventario_global
          </h3>
          <p class="text-xs mt-1 <?= $vistaInventario ? 'text-emerald-700' : 'text-slate-600' ?>">
            <?php if ($vistaInventario): ?>
              ✓ Vista encontrada en la base central. Contiene datos federados (<?= count($filasInventario) ?> prendas consultadas en esta muestra).
            <?php elseif ($pdo): ?>
              ✗ Conectó a la base central, pero no se encontró la vista <code>vista_inventario_global</code>.
            <?php else: ?>
              Pendiente de conexión previa.
            <?php endif; ?>
          </p>
          <?php if ($errorVista): ?>
            <p class="text-[11px] text-red-600 mt-2 bg-white/70 p-2 rounded font-mono">
              <?= htmlspecialchars($errorVista) ?>
            </p>
          <?php endif; ?>
        </div>
        <span class="text-xs font-bold px-2.5 py-1 rounded-full uppercase <?= $vistaInventario ? 'bg-emerald-200 text-emerald-800' : 'bg-slate-200 text-slate-700' ?>">
          <?= $vistaInventario ? 'Lista' : 'Pendiente' ?>
        </span>
      </div>

      <!-- Tablas Transaccionales -->
      <?php if ($pdo): ?>
        <div class="p-4 rounded-xl border bg-slate-50 border-slate-200 text-xs">
          <h4 class="font-bold text-slate-800 mb-2">Estado de Tablas para Checkout:</h4>
          <ul class="space-y-1 text-slate-600">
            <li>pedidos_web: <b><?= ($tablasTransaccionales['pedidos_web'] ?? false) ? '✓ Existe en la base central' : '✗ No existe' ?></b></li>
            <li>detalle_pedidos_web: <b><?= ($tablasTransaccionales['detalle_pedidos_web'] ?? false) ? '✓ Existe en la base central' : '✗ No existe' ?></b></li>
          </ul>
        </div>
      <?php endif; ?>

    </div>

    <!-- Muestra de Datos si hay conexión -->
    <?php if (!empty($filasInventario)): ?>
      <div class="mt-6 pt-6 border-t">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">Muestra en Tiempo Real de vista_inventario_global:</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs border border-slate-200">
            <thead class="bg-slate-100 text-slate-600 uppercase text-[10px]">
              <tr>
                <th class="p-2 border-b">Sucursal</th>
                <th class="p-2 border-b">SKU</th>
                <th class="p-2 border-b">Prenda</th>
                <th class="p-2 border-b">Talla</th>
                <th class="p-2 border-b">Color</th>
                <th class="p-2 border-b">Stock</th>
                <th class="p-2 border-b">Precio</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($filasInventario as $f): ?>
                <tr class="border-b hover:bg-slate-50">
                  <td class="p-2 font-medium text-slate-900"><?= htmlspecialchars($f['sucursal'] ?? '') ?></td>
                  <td class="p-2 font-mono"><?= htmlspecialchars($f['sku'] ?? '') ?></td>
                  <td class="p-2"><?= htmlspecialchars($f['nombre'] ?? '') ?></td>
                  <td class="p-2"><?= htmlspecialchars($f['talla'] ?? '') ?></td>
                  <td class="p-2"><?= htmlspecialchars($f['color'] ?? '') ?></td>
                  <td class="p-2 font-bold text-emerald-700"><?= htmlspecialchars($f['existencias'] ?? 0) ?></td>
                  <td class="p-2">$<?= number_format((float)($f['precio'] ?? 0), 2) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>

    <div class="mt-8 pt-4 border-t flex justify-between items-center text-xs">
      <a href="index.php" class="text-amber-700 hover:underline font-semibold">← Ir a la Tienda (index.php)</a>
      <button onclick="location.reload()" class="bg-slate-900 text-white px-4 py-2 rounded text-xs font-medium hover:bg-slate-800">
        Volver a probar conexión
      </button>
    </div>

  </div>
</body>
</html>
