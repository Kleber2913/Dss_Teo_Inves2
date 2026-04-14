

CREATE DATABASE IF NOT EXISTS api_productos
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE api_productos;

CREATE TABLE IF NOT EXISTS productos (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  nombre    VARCHAR(100)   NOT NULL,
  precio    DECIMAL(10,2)  NOT NULL,
  cantidad  INT            NOT NULL DEFAULT 0,
  creado_en TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
);

-- Datos de ejemplo
INSERT INTO productos (nombre, precio, cantidad) VALUES
 
