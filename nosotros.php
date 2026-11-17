<?php
/**
 * ============================================================
 * BOUTIQUE MIGAJAS — Página Institucional (Nosotros)
 * Archivo: nosotros.php
 * ============================================================
 */

$pageTitle = 'Nosotros · Boutique Migajas';
$activeCategory = 'nosotros';
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
  
  <!-- Encabezado de la página -->
  <div class="text-center max-w-2xl mx-auto mb-16">
    <span class="text-xs uppercase tracking-[0.3em] text-gold font-bold">Nuestra Historia</span>
    <h1 class="font-serif text-3xl sm:text-5xl font-bold text-brown mt-2">
      Estilo, Bienestar y Pasión por la Moda
    </h1>
    <p class="mt-4 text-sm text-brown-light leading-relaxed">
      Nacimos con la convicción de que la elegancia no es ostentación, sino comodidad y confianza. De prenda en prenda, creamos atuendos que acompañan los mejores momentos de tu vida.
    </p>
  </div>

  <!-- Sección de Filosofía y Visión -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center mb-16">
    <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-luxury bg-cream-hover">
      <img
        src="assets/images/banner.png"
        alt="Boutique Migajas Estilo"
        class="w-full h-full object-cover object-center"
        onerror="this.src='https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1200&q=80'"
      />
    </div>
    <div class="space-y-4">
      <span class="text-xs font-bold tracking-widest text-gold uppercase">Compromiso Migajero</span>
      <h2 class="font-serif text-2xl sm:text-3xl font-bold text-brown">
        Prendas seleccionadas con amor y diseño atemporal
      </h2>
      <p class="text-xs sm:text-sm text-brown-light leading-relaxed">
        Cada blusa, vestido y conjunto de nuestra colección pasa por un meticuloso proceso de selección. Buscamos textiles de textura suave, caídas naturales y cortes que favorezcan la silueta femenina en cualquier temporada.
      </p>
      <div class="pt-2 flex items-center gap-6 text-xs text-brown">
        <div>
          <strong class="font-serif text-2xl text-gold block">3</strong>
          <span class="text-brown-muted">Sucursales </span>
        </div>
        <div class="border-l border-brown/10 pl-6">
          <strong class="font-serif text-2xl text-gold block">100%</strong>
          <span class="text-brown-muted">Atención Personalizada</span>
        </div>
        <div class="border-l border-brown/10 pl-6">
          <strong class="font-serif text-2xl text-gold block">24/48h</strong>
          <span class="text-brown-muted">Despacho Express</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Sucursales Físicas de la Red Federada -->
  <div class="bg-white rounded-2xl p-8 sm:p-12 border border-brown/10 shadow-sm mb-12">
    <div class="text-center max-w-xl mx-auto mb-10">
      <span class="text-xs uppercase tracking-widest text-gold font-bold">Presencia Regional</span>
      <h3 class="font-serif text-2xl sm:text-3xl font-bold text-brown mt-1">
        Nuestras Sucursales
      </h3>
      <p class="text-xs text-brown-light mt-2">
        Visítanos en cualquiera de nuestros nodos físicos en la Comarca Lagunera.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      
      <!-- Sucursal A -->
      <div class="p-6 bg-cream/40 rounded-xl border border-brown/5 text-center">
        <div class="w-10 h-10 bg-gold/10 text-gold rounded-full flex items-center justify-center mx-auto mb-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" strokeWidth="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" strokeWidth="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
        </div>
        <h4 class="font-serif text-base font-bold text-brown">Sucursal Lerdo</h4>
        <p class="text-xs text-gold font-medium mt-0.5">Tienda Matriz</p>
        <p class="text-xs text-brown-muted mt-2">
          Av. Francisco I. Madero #100, Centro Histórico, Cd. Lerdo, Dgo.
        </p>
      </div>

      <!-- Sucursal B -->
      <div class="p-6 bg-cream/40 rounded-xl border border-brown/5 text-center">
        <div class="w-10 h-10 bg-gold/10 text-gold rounded-full flex items-center justify-center mx-auto mb-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" strokeWidth="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" strokeWidth="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
        </div>
        <h4 class="font-serif text-base font-bold text-brown">Sucursal Gómez Palacio</h4>
        <p class="text-xs text-gold font-medium mt-0.5">Nodo Comercial</p>
        <p class="text-xs text-brown-muted mt-2">
          Paseo Gómez Palacio, Plaza Central, Gómez Palacio, Dgo.
        </p>
      </div>

      <!-- Sucursal C -->
      <div class="p-6 bg-cream/40 rounded-xl border border-brown/5 text-center">
        <div class="w-10 h-10 bg-gold/10 text-gold rounded-full flex items-center justify-center mx-auto mb-3">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" strokeWidth="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" strokeWidth="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
        </div>
        <h4 class="font-serif text-base font-bold text-brown">Sucursal Torreón</h4>
        <p class="text-xs text-gold font-medium mt-0.5">Nodo Metropolitano</p>
        <p class="text-xs text-brown-muted mt-2">
          Blvd. Independencia #450, Centro Histórico, Torreón, Coah.
        </p>
      </div>

    </div>
  </div>

  <!-- Botón de regreso -->
  <div class="text-center">
    <a
      href="index.php"
      class="inline-block bg-gold hover:bg-gold-dark text-white px-8 py-3 rounded text-xs font-bold tracking-widest uppercase transition-colors shadow-md"
    >
      Ver Prendas Disponibles
    </a>
  </div>

</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
