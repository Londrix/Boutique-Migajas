<?php
/**
 * ============================================================
 * BOUTIQUE MIGAJAS — Encabezado y Navbar (PHP + Tailwind CDN)
 * Archivo: includes/header.php
 * ============================================================
 */
if (!isset($pageTitle)) {
    $pageTitle = 'Boutique Migajas · Estilo & Bienestar';
}
if (!isset($activeCategory)) {
    $activeCategory = 'todos';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Boutique Migajas — Moda femenina y bienestar en nuestra red federada de sucursales." />
  <meta name="theme-color" content="#C29B38" />
  <title><?= htmlspecialchars($pageTitle) ?></title>

  <!-- Favicon -->
  <link rel="icon" type="image/jpeg" href="assets/images/logo.jpe" />

  <!-- Google Fonts: Playfair Display + Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&display=swap" rel="stylesheet" />

  <!-- Tailwind CSS CDN sin necesidad de compiladores Node.js -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            cream: {
              DEFAULT: '#F7F4EE',
              light: '#FAF6EE',
              hover: '#EDE8DF',
            },
            gold: {
              DEFAULT: '#C29B38',
              light: '#D4AE55',
              dark: '#A07C20',
              accent: '#B69248',
            },
            brown: {
              DEFAULT: '#3A2E2B',
              deep: '#2B2320',
              light: '#6B5750',
              muted: '#8A756E',
            },
            dark: {
              DEFAULT: '#121212',
              soft: '#1E1E1E',
              mid: '#2A2A2A',
            },
          },
          fontFamily: {
            serif: ['"Playfair Display"', 'Georgia', 'serif'],
            sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
          },
          boxShadow: {
            'luxury': '0 10px 30px -5px rgba(58, 46, 43, 0.08), 0 4px 6px -2px rgba(58, 46, 43, 0.04)',
            'luxury-hover': '0 20px 40px -5px rgba(58, 46, 43, 0.15), 0 8px 12px -3px rgba(58, 46, 43, 0.06)',
          }
        }
      }
    }
  </script>

  <style>
    /* Suavizado de tipografías y scrollbars */
    body {
      background-color: #F7F4EE;
      color: #3A2E2B;
      font-family: 'Inter', system-ui, sans-serif;
    }
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #F7F4EE; }
    ::-webkit-scrollbar-thumb { background: #C29B38; border-radius: 9999px; }
    ::-webkit-scrollbar-thumb:hover { background: #A07C20; }
  </style>
</head>
<body class="bg-cream text-brown antialiased flex flex-col min-h-screen">

  <!-- ============================================================
       TOP BAR & HEADER
  ============================================================ -->
  <header class="sticky top-0 z-40 bg-cream/95 backdrop-blur-md border-b border-brown/10 shadow-sm transition-all">
    <!-- Anuncio superior de envío -->
    <div class="bg-gold text-white text-[11px] font-medium tracking-widest text-center py-1.5 px-4 uppercase">
      Envío sin costo en compras mayores a $1,200 MXN 
    </div>

    <!-- Barra Principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
      
      <!-- Lado Izquierdo: Botón Búsqueda y Hamburguesa Móvil -->
      <div class="flex items-center space-x-2 w-1/4">
        <button
          type="button"
          id="btn-toggle-search"
          class="p-2 text-brown hover:text-gold transition-colors rounded-full hover:bg-cream-hover"
          aria-label="Buscar prendas"
        >
          <svg class="w-5 h-5 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="7" />
            <line x1="16.5" y1="16.5" x2="22" y2="22" />
          </svg>
        </button>

        <button
          type="button"
          id="btn-mobile-menu"
          class="md:hidden p-2 text-brown hover:text-gold transition-colors"
          aria-label="Abrir menú de navegación"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>

      <!-- Centro: Logo "Boutique Migajas - Estilo & Bienestar" -->
      <div class="flex-1 text-center">
        <a href="index.php" class="inline-flex flex-col items-center group">
          <span class="font-serif text-2xl sm:text-3xl font-bold tracking-wider text-gold group-hover:text-gold-dark transition-colors uppercase">
            Boutique Migajas
          </span>
          <span class="text-[10px] sm:text-xs tracking-[0.3em] uppercase text-brown-light font-light -mt-1">
            Estilo &amp; Bienestar
          </span>
        </a>
      </div>

      <!-- Lado Derecho: Carrito de Compras -->
      <div class="flex items-center justify-end space-x-4 w-1/4">
        <button
          type="button"
          onclick="window.BoutiqueCart.openDrawer()"
          class="relative p-2 text-brown hover:text-gold transition-colors rounded-full hover:bg-cream-hover"
          aria-label="Bolsa de compras"
        >
          <svg class="w-6 h-6 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M6 2 3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
            <line x1="3" y1="6" x2="21" y2="6" />
            <path d="M16 10a4 4 0 01-8 0" />
          </svg>
          <!-- Contador Dinámico -->
          <span class="js-cart-count hidden absolute -top-1 -right-1 bg-gold text-white text-[11px] font-bold w-5 h-5 rounded-full flex items-center justify-center shadow-md">
            0
          </span>
        </button>
      </div>

    </div>

    <!-- Barra de búsqueda desplegable -->
    <div id="search-dropdown-bar" class="hidden bg-cream-hover border-t border-brown/10 py-3 px-4 transition-all">
      <form action="index.php" method="GET" class="max-w-xl mx-auto flex items-center">
        <input
          type="search"
          name="q"
          placeholder="Buscar prendas por nombre, SKU o estilo..."
          value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
          class="w-full bg-white border border-brown/20 rounded-l-md px-4 py-2 text-xs sm:text-sm text-brown placeholder-brown/40 focus:outline-none focus:border-gold"
        />
        <button
          type="submit"
          class="bg-gold hover:bg-gold-dark text-white px-5 py-2 text-xs font-semibold uppercase tracking-wider rounded-r-md transition-colors"
        >
          Buscar
        </button>
      </form>
    </div>

    <!-- Menú de Navegación Horizontal Desktop -->
    <nav class="border-t border-brown/10 hidden md:block">
      <ul class="max-w-7xl mx-auto px-4 flex justify-center space-x-8 py-3 text-xs font-medium tracking-widest uppercase">
        <?php
        $navLinks = [
            'todos'       => ['label' => 'INICIO',             'href' => 'index.php'],
            'blusas'      => ['label' => 'BLUSAS',             'href' => 'index.php?categoria=blusas'],
            'vestidos'    => ['label' => 'VESTIDOS',           'href' => 'index.php?categoria=vestidos'],
            'conjuntos'   => ['label' => 'CONJUNTOS',          'href' => 'index.php?categoria=conjuntos'],
            'pantalones'  => ['label' => 'PANTALONES / FALDAS','href' => 'index.php?categoria=pantalones'],
            'novedades'   => ['label' => 'NOVEDADES',          'href' => 'index.php?categoria=novedades'],
            'nosotros'    => ['label' => 'NOSOTROS',           'href' => 'nosotros.php'],
        ];

        foreach ($navLinks as $key => $link):
            $isActive = ($activeCategory === $key);
        ?>
          <li>
            <a
              href="<?= $link['href'] ?>"
              class="relative py-1 transition-colors hover:text-gold <?= $isActive ? 'text-gold font-bold' : 'text-brown' ?>"
            >
              <?= $link['label'] ?>
              <?php if ($isActive): ?>
                <span class="absolute bottom-0 left-0 right-0 h-[2px] bg-gold rounded-full"></span>
              <?php endif; ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>

    <!-- Menú Móvil Desplegable -->
    <nav id="mobile-nav-menu" class="hidden md:hidden border-t border-brown/10 bg-cream-light py-4 px-6 space-y-2">
      <?php foreach ($navLinks as $key => $link): ?>
        <a
          href="<?= $link['href'] ?>"
          class="block py-2 text-xs font-medium tracking-wider uppercase transition-colors <?= ($activeCategory === $key) ? 'text-gold font-bold pl-2 border-l-2 border-gold' : 'text-brown hover:text-gold' ?>"
        >
          <?= $link['label'] ?>
        </a>
      <?php endforeach; ?>
    </nav>
  </header>

  <!-- ============================================================
       DRAWER LATERAL DE BOLSA DE COMPRAS (#cart-drawer)
  ============================================================ -->
  <div id="cart-drawer" class="hidden fixed inset-0 z-50 overflow-hidden" aria-labelledby="cart-title" role="dialog" aria-modal="true">
    <!-- Backdrop oscuro -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-xs transition-opacity" onclick="window.BoutiqueCart.closeDrawer()"></div>

    <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
      <div class="w-screen max-w-md bg-white shadow-2xl flex flex-col border-l border-brown/10">
        
        <!-- Header del Carrito -->
        <div class="p-5 border-b border-brown/10 bg-cream flex items-center justify-between">
          <div>
            <h2 id="cart-title" class="font-serif text-lg font-bold text-brown">Bolsa de Compras</h2>
            <span id="cart-drawer-count" class="text-xs text-brown-light font-medium">0 prendas seleccionadas</span>
          </div>
          <button type="button" onclick="window.BoutiqueCart.closeDrawer()" class="p-2 text-brown hover:text-gold rounded-full hover:bg-cream-hover transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <!-- Lista de Artículos (Renderizado dinámico en cart.js) -->
        <div id="cart-drawer-items" class="flex-1 overflow-y-auto p-5 space-y-4"></div>

        <!-- Footer del Drawer con Totales y Checkout -->
        <div id="cart-drawer-footer" class="p-5 border-t border-brown/10 bg-cream space-y-4 hidden">
          <div class="space-y-1.5 text-xs">
            <div class="flex justify-between text-brown-light">
              <span>Subtotal:</span>
              <span id="cart-drawer-subtotal">$0.00 MXN</span>
            </div>
            <div class="flex justify-between text-brown-light">
              <span>Envío:</span>
              <span id="cart-drawer-envio" class="text-emerald-700 font-medium">Calculando...</span>
            </div>
            <div class="flex justify-between text-sm font-bold text-brown pt-2 border-t border-brown/10">
              <span>Total estimado:</span>
              <span id="cart-drawer-total" class="text-base text-gold font-serif">$0.00 MXN</span>
            </div>
          </div>

          <div class="space-y-2">
            <a
              href="checkout.php"
              class="w-full bg-gold hover:bg-gold-dark text-white py-3.5 px-4 rounded text-xs font-bold tracking-widest uppercase transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2"
            >
              <span>Proceder al pago</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" strokeWidth="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
            </a>
            <div class="flex justify-between items-center px-1">
              <button type="button" onclick="window.BoutiqueCart.clearCart()" class="text-[11px] text-brown-light hover:text-red-600 underline">
                Vaciar bolsa
              </button>
            
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Contenedor Principal -->
  <main class="flex-1">
