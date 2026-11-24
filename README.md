# EcoRuta Perú - API REST MVC

Proyecto para el curso de Programación Web Básica (NRC 3657) — ISIL 2026-20.  
**Estudiante:** Jose Alberto Fernandez Poma (`45612149@mail.isil.pe`)  
**Docente:** Prof. Luiggui Enrique Ynga Becerra  
**Repositorio:** [https://github.com/wolfjafp/ecoruta-peru](https://github.com/wolfjafp/ecoruta-peru)  

---

## Descripción del Proyecto

Este proyecto corresponde a la Evaluación Continua 4 (PA4) y consiste en una API REST desarrollada en PHP nativo bajo arquitectura **Modelo - Vista - Controlador (MVC)**, conectada mediante PDO a la base de datos MySQL `ecoruta_peru_db`.

El backend implementa las operaciones CRUD para las 6 tablas del modelo de datos de EcoRuta Perú:
* **Regiones (`/api/regiones`):** Zonas geográficas (Costa, Sierra, Selva).
* **Categorías (`/api/categorias`):** Tipos de expedición (Trekking de altura, Senderismo ligero, etc.).
* **Rutas (`/api/rutas`):** Catálogo principal con JOIN a regiones y categorías, y buscador dinámico con LIKE (`/api/rutas/buscar?q=...`).
* **Guías (`/api/guias`):** Personal acreditado con carnet SERNANP.
* **Usuarios (`/api/usuarios`):** Senderistas registrados.
* **Reservas (`/api/reservas`):** Módulo transaccional que relaciona ruta, cliente y guía.

Además, el repositorio incluye la interfaz web desarrollada en la PA2 dentro de la carpeta `frontend/` y el script de base de datos en `database/`.

---

## Requisitos

* XAMPP con Apache y MySQL (PHP 8.1 o superior con extensión `pdo_mysql`).
* Postman o extensión Thunder Client en VS Code para probar las peticiones HTTP.
* Navegador web.

---

## Instalación y Configuración

### 1. Base de Datos
1. Iniciar el servicio MySQL desde el panel de control de XAMPP.
2. Abrir phpMyAdmin (`http://localhost/phpmyadmin`) o MySQL Workbench.
3. Importar el archivo SQL ubicado en:
   ```text
   database/ecoruta_peru_db.sql
   ```
   El script creará la base de datos `ecoruta_peru_db`, las 6 tablas con sus claves primarias y foráneas, y cargará los datos de prueba iniciales.

### 2. Variables de Entorno
En la raíz del proyecto, crear una copia del archivo `.env.example` y renombrarlo como `.env`:
```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=ecoruta_peru_db
DB_USER=root
DB_PASS=
```
*(Nota: Siguiendo las directivas de seguridad de la rúbrica, el archivo `.env` está en `.gitignore` para no exponer credenciales en el repositorio).*

---

## Cómo Levantar el Proyecto en Local

### 1. Servidor Backend (API)
Abrir una terminal en la carpeta del proyecto y ejecutar el servidor embebido de PHP:
```bash
php -S localhost:8000 -t public
```
La API quedará disponible en `http://localhost:8000`.

Para comprobar que responde:
* `http://localhost:8000/` (Mensaje de bienvenida y estado)
* `http://localhost:8000/api/rutas` (Listado de rutas en formato JSON)

*(También se puede copiar la carpeta a `C:\xampp\htdocs\ecoruta-peru\` para usar Apache).*

### 2. Interfaz Web (Frontend)
Para ver la web maquetada en la PA2:
* Abrir el archivo `frontend/index.html` en el navegador con doble clic.

---

## Pruebas de Endpoints con Postman

En la carpeta `tests/` se incluye la colección exportada lista para usar:
```text
tests/pruebas_postman.json
```

**Pasos para probar:**
1. Abrir Postman (o Thunder Client).
2. Hacer clic en **Import** y seleccionar el archivo `tests/pruebas_postman.json`.
3. La colección contiene las peticiones organizadas para probar todos los métodos (GET, POST, PUT, DELETE) y verificar los códigos de estado HTTP (200, 201, 400, 404 y 422).
