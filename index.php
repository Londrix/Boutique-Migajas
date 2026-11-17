<?php
/**
 * ============================================================
 * BOUTIQUE MIGAJAS — Catálogo Principal (PHP Nativo + PostgreSQL)
 * Archivo: index.php
 * ============================================================
 */

require_once __DIR__ . '/config/db.php';

// Filtros desde query string
$categoria = trim($_GET['categoria'] ?? 'todos');
$busqueda  = trim($_GET['q'] ?? '');
$sucursal  = trim($_GET['sucursal'] ?? '');
$orden     = trim($_GET['orden'] ?? 'recientes');

// Conexión PDO a PostgreSQL Central
$pdo = getDBConnection();
$listaSucursales = obtenerListaSucursales($pdo);
$productos = obtenerInventarioGlobal($pdo, $categoria, $busqueda, $sucursal);

// Ordenamiento de los productos
if ($orden === 'precio_asc') {
    usort($productos, fn($a, $b) => $a['precio'] <=> $b['precio']);
} elseif ($orden === 'precio_desc') {
    usort($productos, fn($a, $b) => $b['precio'] <=> $a['precio']);
}

// Configuración de la cabecera
$pageTitle = 'Boutique Migajas · Catálogo Exclusivo';
$activeCategory = $categoria;
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

  <!-- ============================================================
       BANNER HERO (Visible en Inicio sin búsqueda activa)
  ============================================================ -->
  <?php if ($categoria === 'todos' && empty($busqueda)): ?>
    <section class="relative rounded-2xl overflow-hidden mb-12 shadow-luxury bg-brown">
      <div class="relative h-72 sm:h-96 w-full">
        <img
          src="assets/images/banner.png"
          alt="Boutique Migajas Colección 2026"
          class="w-full h-full object-cover object-center opacity-85"
          onerror="this.src='https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1600&q=80'"
        />
        <div class="absolute inset-0 bg-gradient-to-r from-brown-deep/85 via-brown/40 to-transparent flex items-center">
          <div class="max-w-xl px-6 sm:px-12 text-white">
            <span class="text-[11px] font-bold tracking-[0.3em] uppercase text-gold-light">
              Colección Exclusiva 2026
            </span>
            <h1 class="font-serif text-3xl sm:text-5xl font-bold mt-2 leading-tight">
              De prenda en prenda, armas el outfit.
            </h1>
            <p className="mt-3 text-sm text-white/80 font-light max-w-md">
              Sucursales de Lerdo, Gómez Palacio y Torreón.
            </p>
            <div class="mt-6 flex gap-3">
              <a
                href="#catalogo-grid"
                class="bg-gold hover:bg-gold-dark text-white px-6 py-2.5 rounded text-xs font-bold tracking-widest uppercase transition-all shadow-md"
              >
                Explorar Catálogo
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- Banner Informativo de Conexión a Base Central -->
  <?php if (!$pdo): ?>
    <div class="mb-6 p-4 bg-amber-50 border border-amber-200 text-amber-900 rounded-lg text-xs flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="text-base">⚡</span>
        <span>
          <b>Modo Catálogo Activo:</b> Mostrando inventario local sincronizado. Para conectar en vivo a PostgreSQL 15, inicia los contenedores de Docker (<code>localhost:5436</code>) y activa <code>extension=pdo_pgsql</code> en PHP.
        </span>
      </div>
    </div>
  <?php endif; ?>

  <!-- ============================================================
       BARRA DE FILTROS & ENCABEZADO
  ============================================================ -->
  <div id="catalogo-grid" class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-6 border-b border-brown/10 mb-8">
    <div>
      <span class="text-xs uppercase tracking-widest text-gold font-semibold">
        <?= !empty($busqueda) ? 'Resultados para: "' . htmlspecialchars($busqueda) . '"' : 'Sucursales' ?>
      </span>
      <h2 class="font-serif text-2xl sm:text-3xl font-bold text-brown mt-0.5">
        <?= $categoria === 'todos' ? 'Colección Actual' : ucfirst(htmlspecialchars($categoria)) ?>
      </h2>
      <span class="text-xs text-brown-light">
        <?= count($productos) ?> <?= count($productos) === 1 ? 'prenda disponible' : 'prendas disponibles' ?>
      </span>
    </div>

    <!-- Filtros de Sucursal y Ordenamiento -->
    <form method="GET" action="index.php#catalogo-grid" class="flex flex-wrap items-center gap-3">
      <?php if (!empty($categoria) && $categoria !== 'todos'): ?>
        <input type="hidden" name="categoria" value="<?= htmlspecialchars($categoria) ?>">
      <?php endif; ?>
      <?php if (!empty($busqueda)): ?>
        <input type="hidden" name="q" value="<?= htmlspecialchars($busqueda) ?>">
      <?php endif; ?>

      <!-- Selector de Sucursal Dinámico desde la Base de Datos -->
      <div class="flex items-center gap-1.5 text-xs">
        <label for="sucursal-select" class="text-brown-light font-medium whitespace-nowrap">Sucursal:</label>
        <select
          id="sucursal-select"
          name="sucursal"
          onchange="this.form.submit()"
          class="bg-white border border-brown/20 rounded px-2.5 py-1.5 text-xs text-brown focus:outline-none focus:border-gold"
        >
          <option value="" <?= empty($sucursal) ? 'selected' : '' ?>>Todas las sucursales</option>
          <?php foreach ($listaSucursales as $sucOpt): 
              $esSeleccionada = (strcasecmp($sucursal, $sucOpt) === 0);
          ?>
            <option value="<?= htmlspecialchars($sucOpt) ?>" <?= $esSeleccionada ? 'selected' : '' ?>>
              <?= htmlspecialchars($sucOpt) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Selector de Ordenamiento -->
      <div class="flex items-center gap-1.5 text-xs">
        <label for="orden-select" class="text-brown-light font-medium whitespace-nowrap">Ordenar:</label>
        <select
          id="orden-select"
          name="orden"
          onchange="this.form.submit()"
          class="bg-white border border-brown/20 rounded px-2.5 py-1.5 text-xs text-brown focus:outline-none focus:border-gold"
        >
          <option value="recientes" <?= $orden === 'recientes' ? 'selected' : '' ?>>Más recientes</option>
          <option value="price_asc" <?= $orden === 'price_asc' ? 'selected' : '' ?>>Precio: menor a mayor</option>
          <option value="price_desc" <?= $orden === 'price_desc' ? 'selected' : '' ?>>Precio: mayor a menor</option>
        </select>
      </div>
    </form>
  </div>

  <!-- ============================================================
       GRID DE PRODUCTOS (4 Columnas responsivas)
  ============================================================ -->
  <?php if (empty($productos)): ?>
    <div class="text-center py-20 bg-white/40 rounded-xl border border-dashed border-brown/20 my-8">
      <svg class="w-12 h-12 text-brown/30 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" strokeWidth="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
      <h3 class="font-serif text-lg font-bold text-brown">No se encontraron prendas</h3>
      <p class="text-xs text-brown-light mt-1">
        Prueba cambiando los filtros de sucursal o seleccionando otra categoría.
      </p>
      <a href="index.php" class="inline-block mt-4 text-xs font-semibold text-gold underline">
        Restablecer todos los filtros
      </a>
    </div>
  <?php else: ?>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 sm:gap-6">
      <?php foreach ($productos as $prod): 
          $prodJson = htmlspecialchars(json_encode($prod), ENT_QUOTES, 'UTF-8');
          $disponible = $prod['total_existencias'] > 0;
      ?>
        <article
          onclick="window.BoutiqueModal.open(<?= $prodJson ?>)"
          class="group bg-white rounded-lg border border-brown/10 overflow-hidden shadow-sm hover:shadow-luxury-hover transition-all duration-300 flex flex-col cursor-pointer"
        >
          <!-- Contenedor Imagen Ratio 3:4 -->
          <div class="relative aspect-[3/4] bg-cream-hover overflow-hidden">
            <img
              src="<?= htmlspecialchars($prod['imagen_url']) ?>"
              alt="<?= htmlspecialchars($prod['nombre']) ?>"
              loading="lazy"
              class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500"
              onerror="this.src='assets/images/productos/blusa.jpg'"
            />

            <!-- Badge de Stock Global -->
            <div class="absolute top-3 left-3 flex flex-col gap-1">
              <?php if ($disponible): ?>
                <span class="bg-emerald-700/90 backdrop-blur-sm text-white text-[10px] font-semibold tracking-wider uppercase px-2.5 py-0.5 rounded shadow-sm">
                  Disponible (<?= $prod['total_existencias'] ?>)
                </span>
              <?php else: ?>
                <span class="bg-brown-light/90 backdrop-blur-sm text-white text-[10px] font-semibold tracking-wider uppercase px-2.5 py-0.5 rounded shadow-sm">
                  Agotado
                </span>
              <?php endif; ?>
            </div>

            <!-- Overlay al pasar el mouse -->
            <div class="absolute inset-x-0 bottom-0 p-3 bg-gradient-to-t from-black/60 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex justify-center">
              <span class="bg-cream text-brown font-serif text-xs font-semibold px-4 py-2 rounded-full shadow-md hover:bg-gold hover:text-white transition-colors">
                Explorar variantes &amp; stock
              </span>
            </div>
          </div>

          <!-- Información de la prenda -->
          <div class="p-4 flex-1 flex flex-col justify-between">
            <div>
              <span class="text-[10px] font-mono tracking-widest text-brown-light uppercase">
                SKU: <?= htmlspecialchars($prod['sku']) ?>
              </span>
              <h3 class="font-serif text-base font-semibold text-brown group-hover:text-gold transition-colors mt-0.5 line-clamp-1">
                <?= htmlspecialchars($prod['nombre']) ?>
              </h3>
            </div>

            <div class="mt-3 pt-2 border-t border-brown/5 flex items-baseline justify-between">
              <span class="text-lg font-bold text-brown">
                $<?= number_format($prod['precio'], 2) ?> MXN
              </span>
              
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
