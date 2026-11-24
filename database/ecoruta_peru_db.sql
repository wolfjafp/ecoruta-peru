-- =============================================================================
-- INSTITUTO SUPERIOR TECNOLOGICO ISIL
-- PROGRAMACION WEB BASICA (NRC 3657) - EVALUACION CONTINUA 4 (PA4)
-- 
-- ESTUDIANTE: Fernandez Poma Jose Alberto
-- CORREO:     45612149@mail.isil.pe
-- DOCENTE:    Prof. Luiggui Enrique Ynga Becerra
-- PROYECTO:   EcoRuta Peru - Base de Datos Relacional para la API REST MVC
-- MOTOR:      MySQL / MariaDB (InnoDB, Charset utf8mb4_spanish_ci)
-- =============================================================================

-- Si la base de datos ya existe, la recreamos para iniciar completamente limpios
DROP DATABASE IF EXISTS ecoruta_peru_db;

CREATE DATABASE ecoruta_peru_db 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_spanish_ci;

USE ecoruta_peru_db;


-- =============================================================================
-- TABLA 1: REGIONES
-- =============================================================================
CREATE TABLE regiones (
    id_region INT AUTO_INCREMENT PRIMARY KEY,
    nombre_region VARCHAR(50) NOT NULL UNIQUE,
    clima_predominante VARCHAR(100) NOT NULL,
    altitud_promedio_msnm INT NOT NULL CHECK (altitud_promedio_msnm >= 0),
    descripcion VARCHAR(255) NULL,
    estado TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;


-- =============================================================================
-- TABLA 2: CATEGORIAS
-- =============================================================================
CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(60) NOT NULL UNIQUE,
    nivel_exigencia VARCHAR(30) NOT NULL,
    descripcion VARCHAR(255) NULL,
    estado TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;


-- =============================================================================
-- TABLA 3: RUTAS
-- =============================================================================
CREATE TABLE rutas (
    id_ruta INT AUTO_INCREMENT PRIMARY KEY,
    id_region INT NOT NULL,
    id_categoria INT NOT NULL,
    nombre_ruta VARCHAR(100) NOT NULL UNIQUE,
    altitud_maxima_msnm INT NOT NULL CHECK (altitud_maxima_msnm >= 0),
    distancia_km DECIMAL(6, 2) NOT NULL CHECK (distancia_km > 0),
    duracion_dias INT NOT NULL DEFAULT 1 CHECK (duracion_dias >= 1),
    dificultad ENUM('Facil', 'Moderado', 'Dificil', 'Experto') NOT NULL DEFAULT 'Moderado',
    precio_referencial DECIMAL(8, 2) NOT NULL CHECK (precio_referencial >= 0),
    requiere_guia_obligatorio BOOLEAN NOT NULL DEFAULT 0,
    estado ENUM('Activo', 'Mantenimiento', 'Cerrado') NOT NULL DEFAULT 'Activo',
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_rutas_regiones 
        FOREIGN KEY (id_region) REFERENCES regiones(id_region)
        ON UPDATE CASCADE 
        ON DELETE RESTRICT,

    CONSTRAINT fk_rutas_categorias 
        FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
        ON UPDATE CASCADE 
        ON DELETE RESTRICT
) ENGINE=InnoDB;


-- =============================================================================
-- TABLA 4: GUIAS_ARRIEROS
-- =============================================================================
CREATE TABLE guias_arrieros (
    id_guia INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(80) NOT NULL,
    apellidos VARCHAR(80) NOT NULL,
    dni CHAR(8) NOT NULL UNIQUE,
    telefono VARCHAR(15) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    certificacion_sernanp VARCHAR(50) NOT NULL,
    experiencia_anios INT NOT NULL DEFAULT 0 CHECK (experiencia_anios >= 0),
    estado ENUM('Disponible', 'En Ruta', 'Inactivo') NOT NULL DEFAULT 'Disponible'
) ENGINE=InnoDB;


-- =============================================================================
-- TABLA 5: USUARIOS_SENDERISTAS
-- =============================================================================
CREATE TABLE usuarios_senderistas (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(80) NOT NULL,
    apellidos VARCHAR(80) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(15) NULL,
    tipo_documento ENUM('DNI', 'Pasaporte', 'Carnet Extranjeria') NOT NULL DEFAULT 'DNI',
    numero_documento VARCHAR(20) NOT NULL UNIQUE,
    condicion_fisica ENUM('Principiante', 'Intermedio', 'Avanzado') NOT NULL DEFAULT 'Principiante',
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- =============================================================================
-- TABLA 6: RESERVAS_TOURS
-- =============================================================================
CREATE TABLE reservas_tours (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    codigo_reserva VARCHAR(15) NOT NULL UNIQUE,
    id_ruta INT NOT NULL,
    id_usuario INT NOT NULL,
    id_guia INT NULL,
    fecha_salida DATE NOT NULL,
    numero_personas INT NOT NULL CHECK (numero_personas > 0 AND numero_personas <= 15),
    total_pago DECIMAL(10, 2) NOT NULL CHECK (total_pago >= 0),
    estado_reserva ENUM('Pendiente', 'Confirmada', 'Completada', 'Cancelada') NOT NULL DEFAULT 'Pendiente',
    observaciones_medicas TEXT NULL,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_reservas_rutas 
        FOREIGN KEY (id_ruta) REFERENCES rutas(id_ruta)
        ON UPDATE CASCADE 
        ON DELETE RESTRICT,

    CONSTRAINT fk_reservas_usuarios 
        FOREIGN KEY (id_usuario) REFERENCES usuarios_senderistas(id_usuario)
        ON UPDATE CASCADE 
        ON DELETE RESTRICT,

    CONSTRAINT fk_reservas_guias 
        FOREIGN KEY (id_guia) REFERENCES guias_arrieros(id_guia)
        ON UPDATE CASCADE 
        ON DELETE SET NULL
) ENGINE=InnoDB;


-- =============================================================================
-- INSERCION DE DATOS DE PRUEBA REALES
-- =============================================================================

-- 1. Regiones
INSERT INTO regiones (nombre_region, clima_predominante, altitud_promedio_msnm, descripcion) VALUES
('Costa', 'Calido arido y templado humedo costero', 150, 'Senderos litorales, lomas estacionales y desiertos costeros'),
('Sierra', 'Frio seco de alta montana con heladas nocturnas', 3800, 'Cordilleras andinas, quebradas profundas y lagunas glaciares'),
('Selva', 'Calido humedo tropical con lluvias frecuentes', 650, 'Bosques de neblina, cataratas y biodiversidad amazonica');

-- 2. Categorias
INSERT INTO categorias (nombre_categoria, nivel_exigencia, descripcion) VALUES
('Trekking de Altura', 'Alto', 'Rutas por encima de los 3500 msnm que exigen aclimatacion previa'),
('Senderismo Ligero', 'Bajo', 'Caminatas recreativas aptas para toda la familia y principiantes'),
('Ecoturismo Vivencial', 'Moderado', 'Rutas combinadas con pernocte en comunidades campesinas y cultura viva'),
('Expedicion Glaciar', 'Extremo', 'Ascensiones tecnicas que requieren equipo especializado de crampones y piolets');

-- 3. Rutas
INSERT INTO rutas (id_region, id_categoria, nombre_ruta, altitud_maxima_msnm, distancia_km, duracion_dias, dificultad, precio_referencial, requiere_guia_obligatorio) VALUES
(2, 1, 'Laguna 69 - Cordillera Blanca', 4600, 14.50, 1, 'Dificil', 120.00, 1),
(2, 1, 'Trekking Salkantay a Machu Picchu', 4650, 74.00, 5, 'Dificil', 850.00, 1),
(1, 2, 'Lomas de Lachay - Circuito Verde', 350, 6.00, 1, 'Facil', 45.00, 0),
(2, 3, 'Canon del Colca - Oasis Sangalle', 3300, 21.00, 2, 'Moderado', 260.00, 1),
(3, 2, 'Catarata Gocta - Bosque de Neblina', 1800, 10.50, 1, 'Moderado', 80.00, 0),
(2, 3, 'Meseta de Marcahuasi', 4000, 12.00, 2, 'Moderado', 110.00, 0),
(2, 4, 'Circuito Cordillera Huayhuash', 5000, 120.00, 8, 'Experto', 1450.00, 1);

-- 4. Guias y Arrieros
INSERT INTO guias_arrieros (nombres, apellidos, dni, telefono, correo, certificacion_sernanp, experiencia_anios, estado) VALUES
('Carlos Daniel', 'Mamani Huanca', '40589123', '984123456', 'carlos.mamani@ecoruta.pe', 'SERNANP-AGP-2021-084', 8, 'Disponible'),
('Lucia Elena', 'Quispe Alvarado', '45891234', '956789123', 'lucia.quispe@ecoruta.pe', 'SERNANP-PNH-2019-112', 6, 'Disponible'),
('Raul Walter', 'Condori Paucar', '32987456', '974321654', 'raul.condori@ecoruta.pe', 'SERNANP-SHMP-2018-045', 12, 'En Ruta'),
('Maritza Rosa', 'Flores Tapia', '47123987', '943219876', 'maritza.flores@ecoruta.pe', 'SERNANP-RNS-2022-205', 4, 'Disponible'),
('Esteban Joel', 'Zuniga Gutierrez', '41234567', '998765432', 'esteban.zuniga@ecoruta.pe', 'SERNANP-PNA-2020-033', 9, 'Inactivo');

-- 5. Usuarios Senderistas
INSERT INTO usuarios_senderistas (nombres, apellidos, correo, telefono, tipo_documento, numero_documento, condicion_fisica) VALUES
('Jose Alberto', 'Fernandez Poma', '45612149@mail.isil.pe', '987654321', 'DNI', '45612149', 'Avanzado'),
('Ana Maria', 'Torres Benavides', 'ana.torres@gmail.com', '991234567', 'DNI', '42158963', 'Intermedio'),
('Jean Pierre', 'Dubois Lefevre', 'jp.dubois@wanderlust.fr', '981234789', 'Pasaporte', 'FRA8945120', 'Avanzado'),
('Valeria Sofia', 'Caceres Montoya', 'valeria.caceres@outlook.com', '976543210', 'DNI', '70258941', 'Principiante'),
('Diego Alonso', 'Vargas Salazar', 'diego.vargas@empresa.com.pe', '965412389', 'DNI', '44896321', 'Intermedio'),
('Camila Paz', 'Rios Mendoza', 'camila.rios@yahoo.es', '954123698', 'DNI', '73698521', 'Principiante');

-- 6. Reservas de Tours
INSERT INTO reservas_tours (codigo_reserva, id_ruta, id_usuario, id_guia, fecha_salida, numero_personas, total_pago, estado_reserva, observaciones_medicas) VALUES
('RES-2026-001', 1, 1, 2, '2026-12-05', 2, 240.00, 'Confirmada', 'Senderista con aclimatacion previa en Huaraz sin sintomas de soroche'),
('RES-2026-002', 2, 3, 3, '2026-12-10', 4, 3400.00, 'Pendiente', 'Grupo frances con dieta vegetariana, requieren botiquin de primeros auxilios'),
('RES-2026-003', 3, 4, NULL, '2026-12-01', 3, 135.00, 'Confirmada', 'Caminata familiar de fin de semana, senderista con asma leve controlada'),
('RES-2026-004', 4, 2, 1, '2026-12-15', 2, 520.00, 'Completada', 'Recorrido finalizado exitosamente con retorno por Cabanaconde'),
('RES-2026-005', 6, 5, 4, '2026-12-20', 5, 550.00, 'Pendiente', 'Campamento en la meseta, solicitan arriero local para transporte de mochilas'),
('RES-2026-006', 7, 1, 3, '2026-12-28', 2, 2900.00, 'Cancelada', 'Cancelacion voluntaria por reprogramacion de vacaciones de fin de anio');


-- =============================================================================
-- PROCEDIMIENTOS ALMACENADOS (Stored Procedures)
-- =============================================================================

DROP PROCEDURE IF EXISTS sp_buscar_rutas_filtro;
DELIMITER //
CREATE PROCEDURE sp_buscar_rutas_filtro(
    IN p_nombre_ruta VARCHAR(100),
    IN p_nombre_region VARCHAR(50),
    IN p_dificultad VARCHAR(20),
    IN p_precio_maximo DECIMAL(8, 2)
)
BEGIN
    SELECT 
        r.id_ruta,
        r.nombre_ruta,
        reg.nombre_region,
        c.nombre_categoria,
        r.altitud_maxima_msnm,
        r.distancia_km,
        r.duracion_dias,
        r.dificultad,
        r.precio_referencial,
        r.requiere_guia_obligatorio,
        r.estado
    FROM rutas r
    INNER JOIN regiones reg ON r.id_region = reg.id_region
    INNER JOIN categorias c ON r.id_categoria = c.id_categoria
    WHERE 
        (p_nombre_ruta IS NULL OR p_nombre_ruta = '' OR r.nombre_ruta LIKE CONCAT('%', p_nombre_ruta, '%'))
        AND (p_nombre_region IS NULL OR p_nombre_region = '' OR reg.nombre_region = p_nombre_region)
        AND (p_dificultad IS NULL OR p_dificultad = '' OR r.dificultad = p_dificultad)
        AND (p_precio_maximo IS NULL OR p_precio_maximo <= 0 OR r.precio_referencial <= p_precio_maximo)
        AND r.estado != 'Cerrado'
    ORDER BY r.precio_referencial ASC;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_resumen_administrativo_reservas;
DELIMITER //
CREATE PROCEDURE sp_resumen_administrativo_reservas()
BEGIN
    SELECT 
        res.codigo_reserva,
        CONCAT(u.apellidos, ', ', u.nombres) AS senderista,
        u.correo AS contacto,
        r.nombre_ruta AS ruta_ecoturistica,
        reg.nombre_region AS region_peru,
        IFNULL(CONCAT(g.apellidos, ', ', g.nombres), 'SIN ASIGNAR') AS guia_oficial,
        res.fecha_salida,
        res.numero_personas AS cupos_reservados,
        res.total_pago AS monto_soles,
        res.estado_reserva
    FROM reservas_tours res
    INNER JOIN rutas r ON res.id_ruta = r.id_ruta
    INNER JOIN regiones reg ON r.id_region = reg.id_region
    INNER JOIN usuarios_senderistas u ON res.id_usuario = u.id_usuario
    LEFT JOIN guias_arrieros g ON res.id_guia = g.id_guia
    ORDER BY res.fecha_salida DESC;
END //
DELIMITER ;
