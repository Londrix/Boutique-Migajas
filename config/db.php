<?php
/**
 * ============================================================
 * BOUTIQUE MIGAJAS — Conexión Central PostgreSQL con PDO
 * Archivo: config/db.php
 *
 * Conecta con la base de datos central federada (boutique_central_db)
 * expuesta en el puerto 5436 de Docker (o red interna 5432).
 * ============================================================
 */

// Cargar variables de entorno desde .env si existe en la raíz
if (file_exists(__DIR__ . '/../.env')) {
    $envLines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envLines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$k, $v] = explode('=', $line, 2);
            $k = trim($k);
            $v = trim($v, " \t\n\r\0\x0B\"'");
            if (!isset($_ENV[$k])) {
                $_ENV[$k] = $v;
                putenv("$k=$v");
            }
        }
    }
}

// Parámetros de conexión a la Base Central
define('DB_HOST', getenv('DB_HOST') ?: (getenv('CENTRAL_DB_HOST') ?: 'localhost'));
define('DB_PORT', getenv('DB_PORT') ?: '5436');
define('DB_NAME', getenv('DB_NAME') ?: (getenv('CENTRAL_DB_NAME') ?: 'boutique_central_db'));
define('DB_USER', getenv('DB_USER') ?: (getenv('CENTRAL_DB_USER') ?: 'postgres'));
define('DB_PASS', getenv('DB_PASSWORD') ?: (getenv('CENTRAL_DB_PASSWORD') ?: 'central_password123'));

/**
 * Obtiene la instancia activa de conexión PDO a PostgreSQL Central.
 *
 * @return PDO|null Retorna el objeto PDO o null si la conexión falla
 */
function getDBConnection(): ?PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    // Verificar si la extensión pdo_pgsql está habilitada en el entorno PHP
    if (!extension_loaded('pdo_pgsql')) {
        error_log('[DB Central] La extensión PHP pdo_pgsql no está habilitada en php.ini');
        return null;
    }

    try {
        $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', DB_HOST, DB_PORT, DB_NAME);
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => 5,
        ];

        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        error_log(sprintf('[DB Central] Error de conexión PostgreSQL (%s:%s): %s', DB_HOST, DB_PORT, $e->getMessage()));
        return null;
    }
}

/**
 * Obtiene la lista única de sucursales existentes en vista_inventario_global.
 *
 * @param PDO|null $pdo Instancia de base de datos
 * @return array Lista de nombres de sucursales reales
 */
function obtenerListaSucursales(?PDO $pdo): array
{
    if (!$pdo) {
        return ['Sucursal Lerdo', 'Sucursal Gómez Palacio', 'Sucursal Torreón'];
    }

    try {
        $stmt = $pdo->query("SELECT DISTINCT sucursal FROM vista_inventario_global WHERE sucursal IS NOT NULL AND TRIM(sucursal) != '' ORDER BY sucursal ASC");
        $sucursales = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return !empty($sucursales) ? $sucursales : ['Sucursal Lerdo', 'Sucursal Gómez Palacio', 'Sucursal Torreón'];
    } catch (Exception $e) {
        error_log('[DB Central] Error al obtener lista de sucursales: ' . $e->getMessage());
        return ['Sucursal Lerdo', 'Sucursal Gómez Palacio', 'Sucursal Torreón'];
    }
}

/**
 * Consulta la vista_inventario_global y consolida las variantes y stock por sucursal.
 *
 * @param PDO|null $pdo Instancia de base de datos
 * @param string $categoria Filtro opcional por categoría
 * @param string $busqueda Filtro de búsqueda en nombre o SKU
 * @param string $sucursal Filtro de sucursal
 * @return array Lista de productos estructurados con sus variantes y stock
 */
function obtenerInventarioGlobal(?PDO $pdo, string $categoria = '', string $busqueda = '', string $sucursal = ''): array
{
    if (!$pdo) {
        return obtenerInventarioFallback($categoria, $busqueda, $sucursal);
    }

    try {
        $sql = "SELECT sucursal, sku, nombre, talla, color, existencias, precio, imagen_url 
                FROM vista_inventario_global 
                WHERE 1=1";
        $params = [];

        if (!empty($busqueda)) {
            $sql .= " AND (nombre ILIKE ? OR sku ILIKE ?)";
            $params[] = '%' . $busqueda . '%';
            $params[] = '%' . $busqueda . '%';
        }

        if (!empty($sucursal)) {
            // Soporte flexible para nombres de sucursales (ej. 'Sucursal A', 'Sucursal A (Lerdo)', 'Lerdo', etc.)
            $patronSucursal = trim($sucursal);
            if (stripos($patronSucursal, 'Sucursal Lerdo') !== false || stripos($patronSucursal, 'Lerdo') !== false) {
                $sql .= " AND (sucursal ILIKE '%Sucursal Lerdo%' OR sucursal ILIKE '%Lerdo%' OR sucursal = 'Lerdo')";
            } elseif (stripos($patronSucursal, 'Sucursal Gómez Palacio') !== false || stripos($patronSucursal, 'Gómez') !== false || stripos($patronSucursal, 'Gomez') !== false) {
                $sql .= " AND (sucursal ILIKE '%Sucursal Gómez Palacio%' OR sucursal ILIKE '%Gómez%' OR sucursal ILIKE '%Gomez%' OR sucursal = 'Gómez Palacio')";
            } elseif (stripos($patronSucursal, 'Sucursal Torreón') !== false || stripos($patronSucursal, 'Torreón') !== false || stripos($patronSucursal, 'Torreon') !== false) {
                $sql .= " AND (sucursal ILIKE '%Sucursal Torreón%' OR sucursal ILIKE '%Torreón%' OR sucursal ILIKE '%Torreon%' OR sucursal = 'Torreón')";
            } else {
                $sql .= " AND sucursal ILIKE ?";
                $params[] = '%' . $patronSucursal . '%';
            }
        }

        $sql .= " ORDER BY sku ASC, talla ASC, color ASC, sucursal ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        if (empty($rows)) {
            return [];
        }

        return consolidarProductos($rows, $categoria);
    } catch (PDOException $e) {
        error_log('[DB Central] Error al consultar vista_inventario_global: ' . $e->getMessage());
        return obtenerInventarioFallback($categoria, $busqueda, $sucursal);
    }
}

/**
 * Agrupa filas planas de la vista federada en productos consolidados por SKU.
 */
function consolidarProductos(array $rows, string $categoria = ''): array
{
    $productosMap = [];

    foreach ($rows as $row) {
        $sku       = $row['sku'];
        $nombre    = $row['nombre'];
        $precio    = (float) ($row['precio'] ?? 0);
        $stock     = (int) ($row['existencias'] ?? 0);
        $sucursal  = $row['sucursal'] ?? 'Central';
        $talla     = !empty($row['talla']) ? $row['talla'] : 'ÚNICA';
        $color     = !empty($row['color']) ? $row['color'] : 'Estándar';
        $imagenUrl = !empty($row['imagen_url']) ? $row['imagen_url'] : 'assets/images/productos/blusa.jpg';

        if (!isset($productosMap[$sku])) {
            $productosMap[$sku] = [
                'sku'               => $sku,
                'nombre'            => $nombre,
                'precio'            => $precio,
                'imagen_url'        => $imagenUrl,
                'total_existencias' => 0,
                'sucursales'        => [],
                'variantes'         => [],
            ];
        }

        // Acumular existencias globales y por sucursal
        $productosMap[$sku]['total_existencias'] += $stock;
        $productosMap[$sku]['sucursales'][$sucursal] = ($productosMap[$sku]['sucursales'][$sucursal] ?? 0) + $stock;

        // Agrupar variantes por talla y color
        $vKey = $talla . '_' . $color;
        if (!isset($productosMap[$sku]['variantes'][$vKey])) {
            $productosMap[$sku]['variantes'][$vKey] = [
                'talla'                    => $talla,
                'color'                    => $color,
                'existencias_totales'      => 0,
                'disponibilidad_sucursales' => [],
            ];
        }

        $productosMap[$sku]['variantes'][$vKey]['existencias_totales'] += $stock;
        $productosMap[$sku]['variantes'][$vKey]['disponibilidad_sucursales'][] = [
            'sucursal'    => $sucursal,
            'existencias' => $stock,
        ];
    }

    $resultado = [];
    foreach ($productosMap as $sku => $prod) {
        // Filtro por categoría si se especificó
        if (!empty($categoria) && $categoria !== 'todos') {
            $catLower = strtolower($categoria);
            $nomLower = strtolower($prod['nombre']);
            $skuLower = strtolower($sku);

            $coincide = match ($catLower) {
                'blusas'     => str_contains($nomLower, 'blusa') || str_starts_with($skuLower, 'blu'),
                'vestidos'   => str_contains($nomLower, 'vestido') || str_starts_with($skuLower, 'ves'),
                'pantalones' => str_contains($nomLower, 'pantal') || str_contains($nomLower, 'falda') || str_starts_with($skuLower, 'pan') || str_starts_with($skuLower, 'fal'),
                'conjuntos'  => str_contains($nomLower, 'conjunto') || str_starts_with($skuLower, 'con'),
                'novedades'  => true,
                default      => true,
            };

            if (!$coincide) {
                continue;
            }
        }

        // Formatear disponibilidad por sucursal
        $disponibilidadSucursales = [];
        foreach ($prod['sucursales'] as $suc => $cant) {
            $disponibilidadSucursales[] = [
                'sucursal'    => $suc,
                'existencias' => $cant,
            ];
        }

        $resultado[] = [
            'sku'                         => $prod['sku'],
            'nombre'                      => $prod['nombre'],
            'precio'                      => $prod['precio'],
            'imagen_url'                  => $prod['imagen_url'],
            'total_existencias'           => $prod['total_existencias'],
            'disponible'                  => $prod['total_existencias'] > 0,
            'disponibilidad_por_sucursal' => $disponibilidadSucursales,
            'variantes'                   => array_values($prod['variantes']),
        ];
    }

    return $resultado;
}

/**
 * Datos de respaldo para catálogo cuando la base de datos central no está disponible.
 */
function obtenerInventarioFallback(string $categoria = '', string $busqueda = '', string $sucursal = ''): array
{
    $mockRows = [
        ['sucursal' => 'Sucursal Lerdo', 'sku' => 'VES-001', 'nombre' => 'Vestido Valentina Floral', 'talla' => 'CH', 'color' => 'Floral Crema', 'existencias' => 3, 'precio' => 720.00, 'imagen_url' => 'assets/images/productos/869d476363e453d690b0d0aa7e82e7d2.png'],
        ['sucursal' => 'Sucursal Gómez Palacio', 'sku' => 'VES-001', 'nombre' => 'Vestido Valentina Floral', 'talla' => 'M', 'color' => 'Floral Crema', 'existencias' => 5, 'precio' => 720.00, 'imagen_url' => 'assets/images/productos/869d476363e453d690b0d0aa7e82e7d2.png'],
        ['sucursal' => 'Sucursal Torreón', 'sku' => 'VES-001', 'nombre' => 'Vestido Valentina Floral', 'talla' => 'G', 'color' => 'Floral Crema', 'existencias' => 2, 'precio' => 720.00, 'imagen_url' => 'assets/images/productos/869d476363e453d690b0d0aa7e82e7d2.png'],
        ['sucursal' => 'Sucursal Lerdo', 'sku' => 'CON-001', 'nombre' => 'Conjunto Sofía Estilizado', 'talla' => 'M', 'color' => 'Café Profundo', 'existencias' => 2, 'precio' => 1250.00, 'imagen_url' => 'assets/images/productos/chamarra.jpg'],
        ['sucursal' => 'Sucursal B (Gómez Palacio)', 'sku' => 'CON-001', 'nombre' => 'Conjunto Sofía Estilizado', 'talla' => 'G', 'color' => 'Café Profundo', 'existencias' => 3, 'precio' => 1250.00, 'imagen_url' => 'assets/images/productos/chamarra.jpg'],
        ['sucursal' => 'Sucursal C (Torreón)', 'sku' => 'PAN-001', 'nombre' => 'Pantalón Paloma Tiro Alto', 'talla' => '28', 'color' => 'Negro Mate', 'existencias' => 4, 'precio' => 600.00, 'imagen_url' => 'assets/images/productos/short.jpg'],
        ['sucursal' => 'Sucursal A (Lerdo)', 'sku' => 'FAL-001', 'nombre' => 'Falda Renata Midi Plisada', 'talla' => 'M', 'color' => 'Crema Suave', 'existencias' => 5, 'precio' => 480.00, 'imagen_url' => 'assets/images/275ece92ca74601a363ac5fda94c2ca9.jpg'],
        ['sucursal' => 'Sucursal B (Gómez Palacio)', 'sku' => 'FAL-001', 'nombre' => 'Falda Renata Midi Plisada', 'talla' => 'G', 'color' => 'Crema Suave', 'existencias' => 5, 'precio' => 480.00, 'imagen_url' => 'assets/images/275ece92ca74601a363ac5fda94c2ca9.jpg'],
        ['sucursal' => 'Sucursal A (Lerdo)', 'sku' => 'BLU-002', 'nombre' => 'Blusa Camelia Lino', 'talla' => 'CH', 'color' => 'Ocre Dorado', 'existencias' => 3, 'precio' => 620.00, 'imagen_url' => 'assets/images/54e799a82873cba72eed8973197d1b5f.jpg'],
        ['sucursal' => 'Sucursal B (Gómez Palacio)', 'sku' => 'BLU-002', 'nombre' => 'Blusa Camelia Lino', 'talla' => 'M', 'color' => 'Ocre Dorado', 'existencias' => 4, 'precio' => 620.00, 'imagen_url' => 'assets/images/54e799a82873cba72eed8973197d1b5f.jpg'],
    ];

    // Aplicar filtros de búsqueda y sucursal sobre el mock
    if (!empty($busqueda)) {
        $q = strtolower($busqueda);
        $mockRows = array_filter($mockRows, fn($r) => str_contains(strtolower($r['nombre']), $q) || str_contains(strtolower($r['sku']), $q));
    }

    if (!empty($sucursal)) {
        $s = strtolower($sucursal);
        $mockRows = array_filter($mockRows, fn($r) => str_contains(strtolower($r['sucursal']), $s));
    }

    return consolidarProductos($mockRows, $categoria);
}
