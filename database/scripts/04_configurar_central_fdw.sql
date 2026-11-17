-- ============================================================
-- BOUTIQUE MIGAJAS — Script para BASE CENTRAL (boutique_central_db)
-- Ejecutar en: Base de datos 'boutique_central_db' (Puerto 5436 en Docker)
-- ============================================================

-- 1. TABLAS TRANSACCIONALES PARA EL E-COMMERCE (Guest Checkout)
CREATE TABLE IF NOT EXISTS pedidos_web (
    id_pedido SERIAL PRIMARY KEY,
    nombre_cliente VARCHAR(150) NOT NULL,
    email_contacto VARCHAR(150) NOT NULL,
    telefono VARCHAR(50) NOT NULL,
    direccion_envio TEXT NOT NULL,
    fecha_pedido TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    estado VARCHAR(50) DEFAULT 'PENDIENTE',
    total NUMERIC(10, 2) NOT NULL,
    id_sucursal_despacho VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS detalle_pedidos_web (
    id_detalle SERIAL PRIMARY KEY,
    id_pedido INT NOT NULL REFERENCES pedidos_web(id_pedido) ON DELETE CASCADE,
    sku_producto VARCHAR(50) NOT NULL,
    talla VARCHAR(50),
    color VARCHAR(50),
    cantidad INT NOT NULL CHECK (cantidad > 0),
    precio_unitario NUMERIC(10, 2) NOT NULL CHECK (precio_unitario >= 0)
);

-- 2. EXTENSIÓN PARA BASES DE DATOS FORÁNEAS (postgres_fdw)
CREATE EXTENSION IF NOT EXISTS postgres_fdw;

-- 3. SERVIDORES FORÁNEOS (Conexión dentro de la red Docker migajas_network)
-- Nota: Dentro de Docker, el host es el nombre del contenedor y el puerto interno es 5432.
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_foreign_server WHERE srvname = 'servidor_sucursal_a') THEN
        CREATE SERVER servidor_sucursal_a
        FOREIGN DATA WRAPPER postgres_fdw
        OPTIONS (host 'db_sucursal_a', port '5432', dbname 'sucursal_a_db');
    END IF;

    IF NOT EXISTS (SELECT 1 FROM pg_foreign_server WHERE srvname = 'servidor_sucursal_b') THEN
        CREATE SERVER servidor_sucursal_b
        FOREIGN DATA WRAPPER postgres_fdw
        OPTIONS (host 'db_sucursal_b', port '5432', dbname 'sucursal_b_db');
    END IF;

    IF NOT EXISTS (SELECT 1 FROM pg_foreign_server WHERE srvname = 'servidor_sucursal_c') THEN
        CREATE SERVER servidor_sucursal_c
        FOREIGN DATA WRAPPER postgres_fdw
        OPTIONS (host 'db_sucursal_c', port '5432', dbname 'sucursal_c_db');
    END IF;
END $$;

-- 4. MAPEADO DE USUARIOS (USER MAPPING)
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_user_mappings WHERE srvname = 'servidor_sucursal_a' AND usename = 'postgres') THEN
        CREATE USER MAPPING FOR postgres
        SERVER servidor_sucursal_a
        OPTIONS (user 'postgres', password 'sucursal_a_password123');
    END IF;

    IF NOT EXISTS (SELECT 1 FROM pg_user_mappings WHERE srvname = 'servidor_sucursal_b' AND usename = 'postgres') THEN
        CREATE USER MAPPING FOR postgres
        SERVER servidor_sucursal_b
        OPTIONS (user 'postgres', password 'sucursal_b_password123');
    END IF;

    IF NOT EXISTS (SELECT 1 FROM pg_user_mappings WHERE srvname = 'servidor_sucursal_c' AND usename = 'postgres') THEN
        CREATE USER MAPPING FOR postgres
        SERVER servidor_sucursal_c
        OPTIONS (user 'postgres', password 'sucursal_c_password123');
    END IF;
END $$;

-- 5. IMPORTACIÓN DE TABLAS FORÁNEAS EN ESQUEMAS LOCALES
CREATE SCHEMA IF NOT EXISTS fdw_a;
CREATE SCHEMA IF NOT EXISTS fdw_b;
CREATE SCHEMA IF NOT EXISTS fdw_c;

IMPORT FOREIGN SCHEMA public FROM SERVER servidor_sucursal_a INTO fdw_a;
IMPORT FOREIGN SCHEMA public FROM SERVER servidor_sucursal_b INTO fdw_b;
IMPORT FOREIGN SCHEMA public FROM SERVER servidor_sucursal_c INTO fdw_c;

-- 6. VISTA UNIFICADA FEDERADA (vista_inventario_global)
-- Une Productos + Inventario de cada nodo para entregarlo consolidado al frontend PHP.
CREATE OR REPLACE VIEW vista_inventario_global AS
SELECT 
    'Sucursal A' AS sucursal,
    p.sku,
    p.nombre,
    i.talla,
    i.color,
    i.existencias,
    p.precio,
    p.imagen_url
FROM fdw_a.productos p
JOIN fdw_a.inventario i ON p.id_producto = i.id_producto

UNION ALL

SELECT 
    'Sucursal B' AS sucursal,
    p.sku,
    p.nombre,
    i.talla,
    i.color,
    i.existencias,
    p.precio,
    p.imagen_url
FROM fdw_b.productos p
JOIN fdw_b.inventario i ON p.id_producto = i.id_producto

UNION ALL

SELECT 
    'Sucursal C' AS sucursal,
    p.sku,
    p.nombre,
    i.talla,
    i.color,
    i.existencias,
    p.precio,
    p.imagen_url
FROM fdw_c.productos p
JOIN fdw_c.inventario i ON p.id_producto = i.id_producto;
