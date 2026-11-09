/**
 * ============================================================
 * BOUTIQUE MIGAJAS — Servicio de Productos
 * Archivo: src/services/productService.js
 *
 * Capa de abstracción entre el frontend y la API central.
 * Cuando el backend esté listo, solo cambia BASE_URL y los
 * endpoints; el resto del frontend no necesita cambios.
 * ============================================================
 */

const BASE_URL = import.meta?.env?.VITE_API_URL ?? 'http://localhost:4000/api';

/* ── Helpers ────────────────────────────────────────────── */
async function apiFetch(path, options = {}) {
  const url = `${BASE_URL}${path}`;
  const res = await fetch(url, {
    headers: { 'Content-Type': 'application/json', ...options.headers },
    ...options,
  });
  if (!res.ok) {
    const error = await res.json().catch(() => ({}));
    throw new Error(error.message ?? `Error ${res.status} en ${url}`);
  }
  return res.json();
}

/* ── Productos ──────────────────────────────────────────── */

/**
 * Obtiene todos los productos con soporte de filtros.
 * @param {Object} params
 * @param {string}  [params.category]   - 'blusas' | 'vestidos' | ...
 * @param {string}  [params.branch]     - 'colima' | 'manzanillo' | ...
 * @param {string}  [params.sort]       - 'price_asc' | 'price_desc' | 'newest'
 * @param {number}  [params.page]       - Paginación
 * @param {number}  [params.limit]      - Items por página (default 16)
 * @returns {Promise<{ products: Product[], total: number, page: number }>}
 */
export async function getProducts(params = {}) {
  const qs = new URLSearchParams(
    Object.entries(params).filter(([, v]) => v !== undefined && v !== '')
  ).toString();
  return apiFetch(`/products${qs ? '?' + qs : ''}`);
}

/**
 * Obtiene un producto por ID, incluyendo variantes y stock por sucursal.
 * @param {string|number} id
 * @returns {Promise<Product>}
 */
export async function getProductById(id) {
  return apiFetch(`/products/${id}`);
}

/**
 * Consulta el stock de un producto desglosado por sucursal.
 * Conecta con la API central que agrega inventarios federados.
 *
 * @param {string|number} productId
 * @param {string}        [variantId]  - ID de variante (talla/color)
 * @returns {Promise<BranchStock[]>}
 *
 * Respuesta esperada:
 * [
 *   { branchId: 'colima',          branchName: 'Colima',          stock: 5 },
 *   { branchId: 'manzanillo',      branchName: 'Manzanillo',      stock: 0 },
 *   { branchId: 'ciudad-guzman',   branchName: 'Ciudad Guzmán',   stock: 2 },
 *   { branchId: 'tecoman',         branchName: 'Tecomán',         stock: 8 },
 * ]
 */
export async function getStockByBranch(productId, variantId) {
  const qs = variantId ? `?variantId=${variantId}` : '';
  return apiFetch(`/products/${productId}/stock${qs}`);
}

/**
 * Obtiene los productos destacados / novedades para el Home.
 * @param {number} [limit=8]
 * @returns {Promise<Product[]>}
 */
export async function getFeaturedProducts(limit = 8) {
  return apiFetch(`/products/featured?limit=${limit}`);
}

/* ── Mock Data (usar mientras no haya backend) ──────────── */
/**
 * Datos de ejemplo para desarrollo.
 * Reemplaza las llamadas a esta función con getProducts() real.
 */
export function getMockProducts() {
  return [
    {
      id: 1,
      name: 'Blusa Florencia',
      category: 'Blusas',
      price: 549,
      salePrice: null,
      image: 'web-fronted/public/blusa.jpg',
      imageAlt: 'Blusa elegante color beige',
      badge: 'Nuevo',
      stock: 'available',  // 'available' | 'low' | 'out'
      stockLabel: 'Disponible',
      branchStock: [
        { branchId: 'colima',        branchName: 'Colima',        stock: 4 },
        { branchId: 'manzanillo',    branchName: 'Manzanillo',    stock: 2 },
        { branchId: 'ciudad-guzman', branchName: 'Cd. Guzmán',    stock: 0 },
        { branchId: 'tecoman',       branchName: 'Tecomán',       stock: 3 },
      ],
    },
    {
      id: 2,
      name: 'Vestido Valentina',
      category: 'Vestidos',
      price: 890,
      salePrice: 720,
      image: '/apps/web-fronted/public/chamarra.jpg',
      imageAlt: 'Vestido largo floral',
      badge: 'Oferta',
      stock: 'low',
      stockLabel: 'Últimas piezas',
      branchStock: [
        { branchId: 'colima',        branchName: 'Colima',        stock: 1 },
        { branchId: 'manzanillo',    branchName: 'Manzanillo',    stock: 0 },
        { branchId: 'ciudad-guzman', branchName: 'Cd. Guzmán',    stock: 1 },
        { branchId: 'tecoman',       branchName: 'Tecomán',       stock: 0 },
      ],
    },
    {
      id: 3,
      name: 'Conjunto Sofía',
      category: 'Conjuntos',
      price: 1250,
      salePrice: null,
      image: '',
      imageAlt: 'Conjunto de blusa y pantalón elegante',
      badge: null,
      stock: 'available',
      stockLabel: 'Disponible',
      branchStock: [
        { branchId: 'colima',        branchName: 'Colima',        stock: 6 },
        { branchId: 'manzanillo',    branchName: 'Manzanillo',    stock: 4 },
        { branchId: 'ciudad-guzman', branchName: 'Cd. Guzmán',    stock: 3 },
        { branchId: 'tecoman',       branchName: 'Tecomán',       stock: 5 },
      ],
    },
    {
      id: 4,
      name: 'Falda Renata',
      category: 'Faldas',
      price: 480,
      salePrice: null,
      image: '',
      imageAlt: 'Falda midi de tela suave',
      badge: null,
      stock: 'available',
      stockLabel: 'Disponible',
      branchStock: null,
    },
    {
      id: 5,
      name: 'Blusa Camelia',
      category: 'Blusas',
      price: 620,
      salePrice: null,
      image: '/apps/web-fronted/public/blusa.jpg',
      imageAlt: 'Blusa estampada multicolor',
      badge: 'Nuevo',
      stock: 'available',
      stockLabel: 'Disponible',
      branchStock: null,
    },
    {
      id: 6,
      name: 'Vestido Aurora',
      category: 'Vestidos',
      price: 1100,
      salePrice: null,
      image: '',
      imageAlt: 'Vestido de noche elegante',
      badge: null,
      stock: 'out',
      stockLabel: 'Agotado',
      branchStock: null,
    },
    {
      id: 7,
      name: 'Pantalón Paloma',
      category: 'Pantalones',
      price: 750,
      salePrice: 600,
      image: '/apps/web-fronted/public/pantalon.jpg',
      imageAlt: 'Pantalón de tela de tiro alto',
      badge: 'Oferta',
      stock: 'low',
      stockLabel: 'Últimas piezas',
      branchStock: null,
    },
    {
      id: 8,
      name: 'Conjunto Luciana',
      category: 'Conjuntos',
      price: 1380,
      salePrice: null,
      image: '/apps/web-fronted/public/productos/869d476363e453d690b0d0aa7e82e7d2.png',
      imageAlt: 'Conjunto casual elegante',
      badge: 'Nuevo',
      stock: 'available',
      stockLabel: 'Disponible',
      branchStock: null,
    },
  ];
}

/* ── JSDoc Types ────────────────────────────────────────── */
/**
 * @typedef {Object} Product
 * @property {number}      id
 * @property {string}      name
 * @property {string}      category
 * @property {number}      price
 * @property {number|null} salePrice
 * @property {string}      image
 * @property {string}      imageAlt
 * @property {string|null} badge       - 'Nuevo' | 'Oferta' | null
 * @property {string}      stock       - 'available' | 'low' | 'out'
 * @property {string}      stockLabel
 * @property {BranchStock[]|null} branchStock
 *
 * @typedef {Object} BranchStock
 * @property {string} branchId
 * @property {string} branchName
 * @property {number} stock
 */
