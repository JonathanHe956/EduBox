# EduBox

Guía de instalación y configuración del proyecto.

## 📋 Requisitos Previos
*   **Git**
*   **PHP** (compatible con tu versión de Laravel)
*   **Composer**
*   **Node.js** y **NPM**
*   **MySQL**

## 🚀 Pasos de Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/JonathanHe956/EduBox.git
cd EduBox
```

### 2. Instalar dependencias
```bash
composer install
npm install
```

### 3. Configurar el entorno
Crea tu archivo de configuración:
*   **Windows:** `copy .env.example .env`
*   **Mac/Linux:** `cp .env.example .env`

> **Importante:** Abre el archivo `.env` y configura tu conexión a la base de datos (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

### 4. Generar clave de aplicación
```bash
php artisan key:generate
```

### 5. Base de Datos y Seeders
Crea la base de datos vacía en tu gestor SQL y luego ejecuta este comando para crear las tablas e insertar los datos iniciales:

```bash
php artisan migrate --seed
```

### 6. Iniciar el proyecto
Necesitarás dos terminales abiertas:

**Terminal 1 (Frontend):**
```bash
npm run dev
```

**Terminal 2 (Servidor):**
```bash
php artisan serve
```
