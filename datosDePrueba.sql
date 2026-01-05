/* Script para generar datos de prueba en la base de datos sist_alcaldia_2026
 La contraseña es: Admin123 */
 
USE sist_alcaldia_2026;

START TRANSACTION;

-- 1) Roles base (por si no existen)
INSERT IGNORE INTO rol (nom_rol) VALUES
('Administrador'),
('Tesorero'),
('Operador');

-- 2) Crear usuario admin (si no existe) y obtener IDs
SET @rol_admin := (SELECT id_rol FROM rol WHERE nom_rol='Administrador' LIMIT 1);

-- Nota: cla_usu debería ir cifrada en producción. Aquí es una clave simple de prueba.
INSERT IGNORE INTO usuario (nom_usu, cla_usu, fky_rol)
VALUES ('admin', '$2y$10$T.ZDyLQC8VLm71jGGNA2yuPyS9ErJAX5D.vmmVTM1Sn5Aeh2hCvwK', @rol_admin); -- cla_usu='Admin123'

SET @usu_admin := (SELECT id_usu FROM usuario WHERE nom_usu='admin' LIMIT 1);

-- 3) Cargar tipos de impuesto (los que diste)
INSERT IGNORE INTO tipo_impuesto (nom_tip, des_tip) VALUES
('DEUDA MOROSA DE CATASTRO', NULL),
('DEUDA ACTUAL DE CATASTRO', NULL),
('SOLVENCIA TIPO A', NULL),
('CEDULA CATASTRAL', NULL),
('SOLVENCIA MUNICIPAL', NULL),
('PATENTE DE INDUSTRIA Y COMERCIO', NULL),
('SOLVENCIA PATENTE', NULL),
('RENOVACION PATENTE', NULL),
('TRAMITACION PATENTE', NULL),
('SOLVENCIA LICORES', NULL),
('RENOVACION LICORES', NULL),
('RENOVACION LICENCIA DE LICORES', NULL),
('PUBLICIDAD Y PROPAGANDA', NULL),
('DECLARACION ESTIMADA', NULL),
('DEFINITIVA INGRESOS BRUTOS', NULL),
('ESPECTACULOS PUBLICOS', NULL),
('PATENTE DE VEHICULO FISCAL', NULL),
('PERMISO EVENTUAL POR ENFERMEDAD LICORES', NULL),
('PERMISO EVENTUAL ESPECTACULOS PUBLICOS LICORES', NULL),
('MULTAS LICORES', NULL),
('TRAMITACION LICENCIA DE LICORES', NULL),
('APUESTAS LICITAS', NULL),
('TRASPASO LICENCIA LICORES', NULL),
('PERMISO MUNICIPALES', NULL),
('PERMISO DE CONSTRUCCION', NULL),
('USO CONFORME', NULL),
('ZONIFICACION', NULL),
('PERMISO DE ROTURA', NULL),
('TERMINAL DE PASAJEROS LA FRIA', NULL),
('MULTAS INGENIERIA', NULL),
('PERMISO TEMPORAL DE LICORES', NULL),
('MANTENIMIENTO ANUAL', NULL),
('LEY DE CONTRATACIONES PUBLICAS', NULL),
('OTROS INGRESOS EXTRAORDINARIOS', NULL);

COMMIT;

-- 4) Procedimiento para generar 50 contribuyentes y 1-5 facturas c/u (enero-marzo 2026)
DROP PROCEDURE IF EXISTS seed_alcaldia_test_data;
DELIMITER $$

CREATE PROCEDURE seed_alcaldia_test_data()
BEGIN
  DECLARE i INT DEFAULT 1;
  DECLARE con_id INT;

  DECLARE inv_count INT;
  DECLARE j INT;

  DECLARE fac_id INT;
  DECLARE det_count INT;
  DECLARE k INT;

  DECLARE tip_id INT;
  DECLARE monto DECIMAL(16,2);
  DECLARE total DECIMAL(16,2);

  DECLARE dias INT;
  DECLARE fecha DATETIME;

  -- Validación rápida: asegurar que exista admin
  IF (SELECT COUNT(*) FROM usuario WHERE id_usu=@usu_admin) = 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No existe el usuario admin. Cree el usuario y reintente.';
  END IF;

  WHILE i <= 50 DO

    -- Crear contribuyente
    INSERT INTO contribuyente
      (nom_con, rif_con, tel_con, ema_con, dir_con, fky_usu_registro, est_registro)
    VALUES
      (
        CONCAT('Contribuyente ', LPAD(i, 3, '0')),
        CONCAT('V-', LPAD(10000000 + i, 8, '0')),                 -- Ej: V-10000001
        CONCAT('0414', LPAD(FLOOR(RAND()*10000000), 7, '0')),     -- Ej: 0414XXXXXXX
        CONCAT('contrib', LPAD(i, 3, '0'), '@correo.com'),
        CONCAT('Calle ', FLOOR(RAND()*50)+1, ', Av ', FLOOR(RAND()*30)+1,
               ', Sector ', FLOOR(RAND()*20)+1),
        @usu_admin,
        'A'
      );

    SET con_id = LAST_INSERT_ID();

    -- Log (opcional pero útil)
    INSERT INTO log_actividad (fky_usu, nom_usu, modulo, accion, detalle, entidad_tipo, entidad_id)
    VALUES (@usu_admin, 'admin', 'Contribuyente', 'CREAR',
            CONCAT('Se creó contribuyente ID=', con_id),
            'contribuyente', con_id);

    -- Cantidad de facturas 1..5
    SET inv_count = FLOOR(RAND()*5) + 1;
    SET j = 1;

    WHILE j <= inv_count DO

      -- Fecha aleatoria entre 2026-01-01 y 2026-03-31 (90 días aprox)
      SET dias = FLOOR(RAND()*90);
      SET fecha = DATE_ADD('2026-01-01 08:00:00', INTERVAL dias DAY);
      SET fecha = DATE_ADD(fecha, INTERVAL FLOOR(RAND()*36000) SECOND); -- + hasta 10h

      -- Crear factura con total temporal 0, luego se actualiza con suma de detalles
      INSERT INTO factura
        (num_fac, fec_fac, fky_usu, fky_con, des_fac, tot_fac, est_pago, est_registro)
      VALUES
        (
          CONCAT('FAC-', DATE_FORMAT(fecha,'%Y%m'), '-', LPAD(i,3,'0'), '-', LPAD(j,2,'0')),
          fecha,
          @usu_admin,
          con_id,
          'Pago de impuestos municipales',
          0.00,
          'A',
          'A'
        );

      SET fac_id = LAST_INSERT_ID();

      INSERT INTO log_actividad (fky_usu, nom_usu, modulo, accion, detalle, entidad_tipo, entidad_id)
      VALUES (@usu_admin, 'admin', 'Facturacion', 'CREAR_FACTURA',
              CONCAT('Se creó factura ID=', fac_id, ' para contribuyente ID=', con_id),
              'factura', fac_id);

      -- Detalles 1..3 por factura
      SET det_count = FLOOR(RAND()*3) + 1;
      SET total = 0.00;
      SET k = 1;

      WHILE k <= det_count DO
        -- Elegir un tipo de impuesto aleatorio activo
        SELECT id_tip INTO tip_id
        FROM tipo_impuesto
        WHERE est_registro='A'
        ORDER BY RAND()
        LIMIT 1;

        SET monto = ROUND((RAND()*900 + 50), 2);  -- 50.00..950.00

        INSERT INTO factura_detalle (fky_fac, fky_tip, monto_det, est_registro)
        VALUES (fac_id, tip_id, monto, 'A');

        SET total = total + monto;
        SET k = k + 1;
      END WHILE;

      -- Actualizar total de factura
      UPDATE factura
      SET tot_fac = total
      WHERE id_fac = fac_id;

      SET j = j + 1;
    END WHILE;

    SET i = i + 1;
  END WHILE;

END$$
DELIMITER ;

-- 5) Ejecutar el seeder
CALL seed_alcaldia_test_data();

-- 6) Verificación rápida (opcional)
SELECT COUNT(*) AS total_contribuyentes_test
FROM contribuyente
WHERE ema_con LIKE 'contrib%@correo.com';

SELECT COUNT(*) AS total_facturas_test
FROM factura f
JOIN contribuyente c ON c.id_con = f.fky_con
WHERE c.ema_con LIKE 'contrib%@correo.com';

SELECT MIN(fec_fac) AS fecha_min, MAX(fec_fac) AS fecha_max
FROM factura f
JOIN contribuyente c ON c.id_con = f.fky_con
WHERE c.ema_con LIKE 'contrib%@correo.com';
