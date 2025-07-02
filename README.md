# 📬 Formulario de Actualización de Correo - Segundo Periodo 2025

Este proyecto es un formulario web desarrollado en Laravel que permite la actualización del correo electrónico de los ciudadanos para el **Registro Nacional del Segundo Periodo 2025**, en cumplimiento de la **Ley Orgánica de Protección de Datos Personales** del Ecuador.

---

## 🧰 Requisitos

- PHP >= 8.0  
- Composer  
- PostgreSQL  
- Extensiones PHP comunes: `pdo`, `pgsql`, `openssl`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `curl`, `fileinfo`

---

## 🛠️ Instalación y ejecución local

```bash
git clone https://github.com/usuario/formulario-correo-2025.git
cd formulario-correo-2025

composer install

cp .env.example .env
php artisan key:generate
```

### ⚙️ Configura el archivo `.env` con tu conexión a la base de datos

```dotenv
APP_NAME=FormularioCorreo
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

LOG_CHANNEL=stack

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=updateEmail
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### 🗄️ Ejecuta las migraciones

```bash
php artisan migrate
```

### 🧼 Si deseas reiniciar la base de datos

```bash
php artisan migrate:fresh
```

### 🔥 Inicia el servidor local

```bash
php artisan serve
```

Luego accede desde tu navegador a: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 📦 Despliegue en servidor (Apache/NGINX)

### 1. Subir archivos al servidor (sin la carpeta `vendor`)

```bash
scp -r * usuario@IP:/ruta/proyecto
```

### 2. Conectarse al servidor y configurar:

```bash
cd /ruta/proyecto

composer install --no-dev --optimize-autoloader

cp .env.example .env
php artisan key:generate

# Edita el archivo .env con credenciales reales

php artisan migrate

# Optimiza configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permisos
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data .
```

> **IMPORTANTE:** Configura `DocumentRoot` del servidor web para que apunte a `/public`.

---

## 🔐 Seguridad

- No subas al servidor los siguientes archivos o carpetas:
  - `.env`
  - `/vendor`
  - `composer.lock`
- Desactiva el modo debug en producción (`APP_DEBUG=false`)
- Revisa los permisos de carpetas `storage` y `bootstrap/cache`

---

## 🧪 Validaciones incluidas

- Código dactilar y fecha de expiración validados contra Registro Civil (DINARDAP)
- Verificación de unicidad por cédula y correo
- Validación de correo sin espacios ni comas
- Aceptación obligatoria de política de privacidad

---

## 📝 Licencia

Proyecto institucional de uso educativo y administrativo interno para la **Secretaría de Educación Superior, Ciencia, Tecnología e Innovación - SENESCYT**.
# postulacionDocente
# postulacionDocente
