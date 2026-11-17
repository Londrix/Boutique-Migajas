<?php
/**
 * ============================================================
 * BOUTIQUE MIGAJAS — API de Productos (PHP Nativo + PostgreSQL)
 * Archivo: api/productos.php
 *
 * Expone en JSON el inventario global de la vista_inventario_global
 * ============================================================
 */

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($requestMethod === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/db.php';

$sku       = trim($_GET['sku'] ?? '');
$categoria = trim($_GET['categoria'] ?? '');
$busqueda  = trim($_GET['q'] ?? '');
$sucursal  = trim($_GET['sucursal'] ?? '');

$pdo = getDBConnection();
$productos = obtenerInventarioGlobal($pdo, $categoria, $busqueda, $sucursal);

if (!empty($sku)) {
    $encontrado = null;
    foreach ($productos as $p) {
        if (strcasecmp($p['sku'], $sku) === 0) {
            $encontrado = $p;
            break;
        }
    }

    if (!$encontrado) {
        http_response_code(404);
        echo json_encode([
            'status'  => 'error',
            'message' => "Producto con SKU '$sku' no encontrado",
        ]);
        exit;
    }

    echo json_encode([
        'status' => 'success',
        'data'   => $encontrado,
    ]);
    exit;
}

echo json_encode([
    'status'          => 'success',
    'total_productos' => count($productos),
    'data'            => $productos,
]);
