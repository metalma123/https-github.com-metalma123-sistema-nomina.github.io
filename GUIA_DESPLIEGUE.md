# Guía de Despliegue Web Gratis (PHP + PostgreSQL)

Esta guía te explicará paso a paso cómo subir tu **Sistema de Nómina** a internet de forma gratuita utilizando **Render** (para la página web) y **Neon.tech** (para la base de datos PostgreSQL).

## 1. Requisitos Previos
*   Tener una cuenta en [GitHub](https://github.com/).
*   Tener instalado **Git** en tu computadora.
*   Tener acceso a tu base de datos local (pgAdmin o herramienta similar).

---

## 2. Preparar el Proyecto
Antes de subir el código, debemos asegurarnos de que todo esté listo.

1.  **Archivo `conexion.php`**: Tu código ya está listo para la nube porque usa variables de entorno (`getenv`). No necesitas cambiar nada ahí.
2.  **Base de Datos**: Necesitas una copia de tu base de datos actual.
    *   Abre **pgAdmin** (o tu gestor de BD).
    *   Haz clic derecho en tu base de datos `sistema_nomina`.
    *   Selecciona **Backup (Copia de Seguridad)**.
    *   Formato: `Plain` (Plano) o `SQL`.
    *   Guarda el archivo como `base_datos.sql` en la carpeta de tu proyecto.

---

## 3. Crear el Repositorio en GitHub
Para que Render pueda leer tu código, debe estar en GitHub.

1.  Ve a [GitHub.com](https://github.com/) e inicia sesión.
2.  Crea un **Nuevo Repositorio** (botón verde "New").
3.  Ponle un nombre (ej. `sistema-nomina`).
4.  Selecciona "Private" (Privado) si no quieres que nadie más vea tu código.
5.  Haz clic en **Create repository**.
6.  Sigue las instrucciones que aparecen para subir tu carpeta `c:\xampp\htdocs\SistemaNomina`:
    *   Abre la terminal (CMD o Git Bash) en la carpeta de tu proyecto.
    *   Ejecuta:
        ```bash
        git init
        git add .
        git commit -m "Mi primer subida"
        git branch -M main
        git remote add origin https://github.com/TU_USUARIO/sistema-nomina.git
        git push -u origin main
        ```

---

## 4. Crear la Base de Datos en Neon.tech
Usaremos Neon porque ofrece una cuenta gratuita generosa para PostgreSQL.

1.  Regístrate en [Neon.tech](https://neon.tech/).
2.  Crea un nuevo proyecto.
3.  Copia la **Connection String** (Cadena de conexión). Se verá algo así:
    `postgres://usuario:password@ep-algo.aws.neon.tech/neondb`
4.  Ve a la pestaña **SQL Editor** en Neon.
5.  Abre tu archivo `base_datos.sql` (que creaste en el paso 2) en un bloc de notas, copia todo el contenido y pégalo en el editor SQL de Neon.
6.  Ejecuta el script para crear tus tablas y datos en la nube.

---

## 5. Configurar Render (La Web)
1.  Regístrate en [Render.com](https://render.com/).
2.  Haz clic en **New +** y selecciona **Web Service**.
3.  Conecta tu cuenta de GitHub y selecciona el repositorio `sistema-nomina` que creaste.
4.  Configuración:
    *   **Name**: Un nombre para tu app (ej. `nomina-web`).
    *   **Runtime**: Docker.
    *   **Repo**: Selecciona tu repositorio.
    
### Crear Dockerfile
Como Render necesita saber cómo ejecutar tu PHP, debes crear un archivo llamado `Dockerfile` (sin extensión) en la raíz de tu proyecto con este contenido:

```dockerfile
FROM php:8.2-apache

# Instalar extensión de PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copiar archivos al servidor
COPY . /var/www/html/

# Configurar Apache para usar el puerto de Render
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Dar permisos al usuario www-data
RUN chown -R www-data:www-data /var/www/html
```

5.  Guarda este archivo en tu carpeta, haz `git add Dockerfile`, `git commit` y `git push`.
6.  De vuelta en Render, en la configuración del Web Service:
    *   **Environment Variables** (Variables de Entorno) -> Add Environment Variable:
        *   `DB_HOST`: (El host de Neon, lo que está después del @ y antes de la /)
        *   `DB_PORT`: `5432`
        *   `DB_NAME`: `neondb` (o el nombre de tu base en Neon)
        *   `DB_USER`: (Tu usuario de Neon)
        *   `DB_PASSWORD`: (Tu contraseña de Neon)
7.  Haz clic en **Create Web Service**.

---

## 6. ¡Listo!
Render tardará unos minutos en construir tu aplicación. Cuando termine, te dará una URL (ej. `https://nomina-web.onrender.com`). ¡Esa es tu página web funcionando!
