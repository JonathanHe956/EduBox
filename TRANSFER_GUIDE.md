# Guía para Transferir el Proyecto Manualmente (Sin Git)

Sigue estos pasos para pasar el proyecto "EduBox" a otra persona y que funcione correctamente en su computadora.

## 1. Preparar los Archivos (En tu computadora)

Antes de comprimir la carpeta, es recomendable limpiar archivos temporales y cachés.

1.  Abre la terminal en la carpeta del proyecto y ejecuta:
    ```bash
    php artisan optimize:clear
    ```
2.  **Base de Datos:**
    *   Necesitas exportar tu base de datos actual para que la otra persona tenga los mismos datos (usuarios, exámenes, etc.).
    *   Usa una herramienta como **HeidiSQL**, **phpMyAdmin** o **MySQL Workbench**.
    *   Exporta la base de datos `edubox` (o el nombre que uses) a un archivo `.sql` (ej. `respaldo_edubox.sql`).
    *   Guarda este archivo dentro de la carpeta del proyecto (por ejemplo, en la raíz).

3.  **Comprimir el Proyecto:**
    *   Selecciona la carpeta del proyecto `EduBox`.
    *   **IMPORTANTE:** Para que el archivo no pese demasiado y evitar errores, **NO** incluyas las carpetas `node_modules` y `vendor`. Estas se regenerarán en la otra computadora.
    *   Si quieres hacerlo simple y la otra persona tiene el mismo sistema operativo (Windows), puedes incluirlas, pero lo estándar es **excluirlas**.
    *   Asegúrate de incluir el archivo `.env` si quieres que tengan tu misma configuración (contraseñas de BD, etc.), o diles que copien el `.env.example`.
    *   Crea un archivo **.zip** o **.rar** con todo el contenido (incluyendo el `respaldo_edubox.sql`).

## 2. Instalar en la Nueva Computadora

La otra persona debe tener instalado:
*   **PHP** (versión 8.2 o superior)
*   **Composer**
*   **Node.js**
*   **MySQL** (XAMPP, Laragon, o MySQL server)

### Pasos para la otra persona:

1.  **Descomprimir:**
    *   Extrae el archivo .zip en una carpeta (ej. `C:\Proyectos\EduBox`).

2.  **Instalar Dependencias:**
    *   Abre una terminal en esa carpeta.
    *   Ejecuta los siguientes comandos (si no enviaste las carpetas `vendor` y `node_modules`):
        ```bash
        composer install
        npm install
        ```

3.  **Configuración del Entorno:**
    *   Si enviaste el archivo `.env`, salta este paso.
    *   Si no, copia el archivo `.env.example` y renómbralo a `.env`.
    *   Abre el archivo `.env` y configura la base de datos:
        ```env
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=edubox  <-- Nombre de la base de datos
        DB_USERNAME=root    <-- Usuario de MySQL
        DB_PASSWORD=        <-- Contraseña de MySQL
        ```

4.  **Base de Datos:**
    *   Crea una base de datos vacía en MySQL con el nombre que pusiste en el `.env` (ej. `edubox`).
    *   Importa el archivo `respaldo_edubox.sql` que venía en el zip usando tu gestor de base de datos (HeidiSQL, phpMyAdmin, etc.).
    *   *Alternativa:* Si no importas el respaldo y quieres una base limpia, ejecuta:
        ```bash
        php artisan migrate --seed
        ```

5.  **Generar Clave (Si es instalación limpia):**
    *   Si copiaste el `.env.example`, ejecuta:
        ```bash
        php artisan key:generate
        ```

6.  **Enlace de Almacenamiento (Imágenes):**
    *   Para que se vean las fotos de perfil y evidencias:
        ```bash
        php artisan storage:link
        ```

7.  **Compilar Assets (Diseño):**
    *   Para generar los estilos CSS y JS:
        ```bash
        npm run build
        ```

8.  **Iniciar el Servidor:**
    *   Finalmente, ejecuta:
        ```bash
        php artisan serve
        ```
    *   Entra a `http://127.0.0.1:8000` en el navegador.

## Resumen Rápido de Comandos (Nueva PC)

```bash
composer install
npm install
npm run build
php artisan key:generate  # Solo si cambió el .env
php artisan storage:link
php artisan serve
```
