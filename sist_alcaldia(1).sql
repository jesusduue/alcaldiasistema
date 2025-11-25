
CREATE DATABASE IF NOT EXISTS sist_alcaldia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sist_alcaldia;

/*
================================================================================
    SECCIÓN 1: GESTIÓN DE ACCESOS Y ENTIDADES BÁSICAS
================================================================================
*/

/* Tabla para gestionar los roles de los usuarios */
CREATE TABLE IF NOT EXISTS rol (
  id_rol INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  nom_rol VARCHAR(50) NOT NULL UNIQUE,          -- Nombre del rol (Ej: Administrador, Tesorero)
  est_registro CHAR(1) NOT NULL DEFAULT 'A'     -- Estado del registro: 'A' activo, 'I' inactivo
) ENGINE=InnoDB;

/* Tabla para gestionar los usuarios del sistema */
CREATE TABLE IF NOT EXISTS usuario (
  id_usu INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  nom_usu VARCHAR(50) NOT NULL UNIQUE,
  cla_usu VARCHAR(255) NOT NULL,                -- Clave de acceso (debe ser cifrada con hash en producción)
  fky_rol INT NOT NULL,
  est_registro CHAR(1) NOT NULL DEFAULT 'A',    -- Estado del registro: 'A' activo, 'I' inactivo
  FOREIGN KEY (fky_rol) REFERENCES rol(id_rol) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

/* Tabla para gestionar los contribuyentes y comercios que pagan impuestos */
CREATE TABLE IF NOT EXISTS contribuyente (
  id_con INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  nom_con VARCHAR(100) NOT NULL,                -- Nombre del contribuyente o razón social
  rif_con VARCHAR(15) NOT NULL UNIQUE,          -- Cédula o RIF del contribuyente
  tel_con VARCHAR(20) NOT NULL,
  ema_con VARCHAR(100) NOT NULL UNIQUE,
  dir_con VARCHAR(255) NOT NULL,
  fky_usu_registro INT NOT NULL,                -- Usuario que realizó el registro
  est_registro CHAR(1) NOT NULL DEFAULT 'A',    -- Estado del registro: 'A' activo, 'I' inactivo
  FOREIGN KEY (fky_usu_registro) REFERENCES usuario(id_usu) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

/*
================================================================================
    SECCIÓN 2: GESTIÓN DE IMPUESTOS Y FACTURACIÓN
================================================================================
*/

/* Tabla para gestionar los tipos de impuestos con valor y descripción */
CREATE TABLE IF NOT EXISTS tipo_impuesto (
  id_tip INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  nom_tip VARCHAR(100) NOT NULL UNIQUE,
  des_tip VARCHAR(255) NULL,                    -- Descripción opcional del impuesto
  est_registro CHAR(1) NOT NULL DEFAULT 'A'     -- Estado del registro: 'A' activo, 'I' inactivo
) ENGINE=InnoDB;

/* Tabla para gestionar las facturas de los contribuyentes */
CREATE TABLE IF NOT EXISTS factura (
  id_fac INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  num_fac VARCHAR(20) NOT NULL UNIQUE,          -- Número de la factura/control (VARCHAR para flexibilidad)
  fec_fac DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Fecha de emisión
  fky_usu INT NOT NULL,
  fky_con INT NOT NULL,
  des_fac VARCHAR(255) NOT NULL,                -- Descripción general (Ej: "Pago de impuestos municipales")
  tot_fac DECIMAL(16,2) NOT NULL,
  est_pago CHAR(1) NOT NULL DEFAULT 'A',        -- Estado del pago: 'A' Activo, 'N' Anulada
  fec_anulacion DATETIME NULL,               -- Fecha de anulación (si aplica)
  est_registro CHAR(1) NOT NULL DEFAULT 'A',    -- Estado del registro: 'A' activo, 'I' inactivo (para borrado lógico)
  FOREIGN KEY (fky_con) REFERENCES contribuyente(id_con) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (fky_usu) REFERENCES usuario(id_usu) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;
/* Tabla para almacenar el detalle de cada factura (lineas por tipo de impuesto) */
CREATE TABLE IF NOT EXISTS factura_detalle (
  id_fde INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  fky_fac INT NOT NULL,
  fky_tip INT NOT NULL,
  monto_det DECIMAL(16,2) NOT NULL,
  est_registro CHAR(1) NOT NULL DEFAULT 'A',    -- Estado del registro: 'A' activo, 'I' inactivo (para borrado lógico)
  FOREIGN KEY (fky_fac) REFERENCES factura(id_fac) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (fky_tip) REFERENCES tipo_impuesto(id_tip) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;
  

/*
================================================================================
    SECCIÓN 3: AUDITORÍA Y REGISTRO DE ACTIVIDADES
================================================================================
*/

CREATE TABLE IF NOT EXISTS log_actividad (
  id_log        INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  fec_log       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fky_usu       INT NOT NULL,
  nom_usu       VARCHAR(50) NOT NULL,
  modulo        VARCHAR(50) NOT NULL,
  accion        VARCHAR(50) NOT NULL,
  detalle       VARCHAR(255) NOT NULL,
  entidad_tipo  VARCHAR(50)  NULL,
  entidad_id    INT          NULL,
  metadata      JSON         NULL,
  ip            VARCHAR(45)  NULL,
  user_agent    VARCHAR(255) NULL,
  est_log       CHAR(1)      NOT NULL DEFAULT 'A',
  CONSTRAINT fk_log_actividad_usuario FOREIGN KEY (fky_usu) REFERENCES usuario(id_usu) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;
