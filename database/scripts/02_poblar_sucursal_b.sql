-- ============================================================
-- BOUTIQUE MIGAJAS — Script para SUCURSAL B (sucursal_b_db)
-- Ejecutar en: Base de datos 'sucursal_b_db' (Puerto 5434 en Docker)
-- ============================================================

-- 1. ESTRUCTURA DE TABLAS (Basada en el Diagrama Relacional)
CREATE TABLE IF NOT EXISTS categorias (
    id_categorias BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS productos (
    id_producto BIGSERIAL PRIMARY KEY,
    sku VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    precio NUMERIC(10, 2) NOT NULL,
    id_categoria BIGINT REFERENCES categorias(id_categorias),
    imagen_url VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS inventario (
    id_inventario BIGSERIAL PRIMARY KEY,
    talla VARCHAR(50) NOT NULL,
    color VARCHAR(50) NOT NULL,
    existencias INT NOT NULL CHECK (existencias >= 0),
    id_producto BIGINT NOT NULL REFERENCES productos(id_producto) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS empleados (
    id_empleado BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    puesto VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS ventas (
    id_ventas BIGSERIAL PRIMARY KEY,
    fecha TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    total NUMERIC(10, 2) NOT NULL,
    id_empleado BIGINT REFERENCES empleados(id_empleado)
);

CREATE TABLE IF NOT EXISTS detalle_venta (
    id_detalle BIGSERIAL PRIMARY KEY,
    cantidad INT NOT NULL CHECK (cantidad > 0),
    precio_unitario NUMERIC(10, 2) NOT NULL,
    id_venta BIGINT NOT NULL REFERENCES ventas(id_ventas) ON DELETE CASCADE,
    id_producto BIGINT NOT NULL REFERENCES productos(id_producto)
);

-- 2. LIMPIEZA DE DATOS PREVIOS
TRUNCATE TABLE detalle_venta, ventas, inventario, productos, categorias, empleados RESTART IDENTITY CASCADE;

-- 3. INSERCIÓN DE CATEGORÍAS
INSERT INTO categorias (id_categorias, nombre) VALUES
(1, 'Blusas'),
(2, 'Vestidos'),
(3, 'Conjuntos'),
(4, 'Pantalones y Faldas');

-- 4. INSERCIÓN DE EMPLEADOS (Sucursal B)
INSERT INTO empleados (id_empleado, nombre, puesto) VALUES
(1, 'Carolina Méndez Ríos', 'Gerente de Tienda'),
(2, 'Daniela Favela Nava', 'Atención al Cliente');

-- 5. INSERCIÓN DE PRODUCTOS
INSERT INTO productos (id_producto, sku, nombre, precio, id_categoria, imagen_url) VALUES
(1, 'VES-001', 'Vestido Valentina Floral', 720.00, 2, 'assets/images/productos/869d476363e453d690b0d0aa7e82e7d2.png'),
(2, 'BLU-001', 'Blusa Florencia Elegance', 549.00, 1, 'assets/images/productos/blusa.jpg'),
(3, 'CON-001', 'Conjunto Sofía Estilizado', 1250.00, 3, 'assets/images/productos/chamarra.jpg'),
(4, 'PAN-001', 'Pantalón Paloma Tiro Alto', 600.00, 4, 'assets/images/productos/short.jpg'),
(5, 'FAL-001', 'Falda Renata Midi Plisada', 480.00, 4, 'assets/images/275ece92ca74601a363ac5fda94c2ca9.jpg'),
(6, 'BLU-002', 'Blusa Camelia Lino', 620.00, 1, 'assets/images/54e799a82873cba72eed8973197d1b5f.jpg');

-- 6. INSERCIÓN DE INVENTARIO (Stock específico de Sucursal B)
INSERT INTO inventario (talla, color, existencias, id_producto) VALUES
-- Vestido Valentina Floral (VES-001)
('CH', 'Floral Crema', 2, 1),
('M',  'Floral Crema', 6, 1),

-- Blusa Florencia Elegance (BLU-001)
('M',  'Beige Dorado', 8, 2),
('G',  'Beige Dorado', 5, 2),

-- Conjunto Sofía Estilizado (CON-001)
('M',  'Café Profundo', 4, 3),
('G',  'Café Profundo', 4, 3),

-- Pantalón Paloma Tiro Alto (PAN-001)
('28', 'Negro Mate', 0, 4), -- Agotado en esta sucursal
('30', 'Negro Mate', 2, 4),

-- Falda Renata Midi Plisada (FAL-001)
('M',  'Crema Suave', 5, 5),
('G',  'Crema Suave', 4, 5),

-- Blusa Camelia Lino (BLU-002)
('CH', 'Ocre Dorado', 3, 6),
('M',  'Ocre Dorado', 5, 6);

-- 7. VENTAS DE MUESTRA (Histórico local en Sucursal B)
INSERT INTO ventas (id_ventas, fecha, total, id_empleado) VALUES
(1, NOW() - INTERVAL '3 days', 620.00, 1),
(2, NOW() - INTERVAL '1 day', 1250.00, 2);

INSERT INTO detalle_venta (cantidad, precio_unitario, id_venta, id_producto) VALUES
(1, 620.00, 1, 6),
(1, 1250.00, 2, 3);
