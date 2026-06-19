# Sistema CUP FICCT

Sistema web de gestión de admisión para el Curso Preuniversitario de la Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones (FICCT) — Universidad Autónoma Gabriel René Moreno (UAGRM).

Desarrollado con **Laravel 11** + **PostgreSQL** siguiendo la metodología **PUDS** en 2 ciclos iterativos.

---

## 🌐 Sistema desplegado en producción

**URL:** https://sistema-web-cup-production.up.railway.app

---

## Tecnologías

- **Backend:** PHP 8.2 + Laravel 11
- **Base de datos:** PostgreSQL
- **Frontend:** Blade Templates + CSS personalizado (colores institucionales UAGRM)
- **Autenticación:** Laravel Auth con RBAC (perfiles y privilegios)
- **Despliegue:** Railway (app + PostgreSQL)

---

## Ciclos de desarrollo (PUDS)

| Ciclo | Casos de Uso | Estado |
|---|---|---|
| **Ciclo 1** | CU-01, CU-02, CU-03, CU-07, CU-09, CU-13, CU-14, CU-15, CU-16, CU-19 | ✅ Completado |
| **Ciclo 2** | CU-04, CU-05, CU-06, CU-08, CU-10, CU-11, CU-12, CU-17, CU-18, CU-20 | ✅ Completado |

---

## Actores del sistema

- **Postulante** — se registra vía formulario público, no tiene cuenta en el sistema
- **Docente** — accede con credenciales, registra notas de su grupo/materia
- **Administrador** — accede con credenciales, gestiona todo el sistema

---

## Requisitos previos (desarrollo local)

- PHP **8.2** o superior con extensión `pdo_pgsql` habilitada
- Composer
- PostgreSQL (pgAdmin o similar)

```bash
php --version
composer --version
```

---

## Instalación local

### 1. Clonar el repositorio

```bash
git clone https://github.com/pithersec/Sistema-Web-CUP.git
cd Sistema-Web-CUP
```

### 2. Instalar dependencias

```bash
composer install
```

### 3. Habilitar extensiones pdo_pgsql y pgqsl

En tu `php.ini` busca y descomenta las siguientes líneas (quita el `;`):

```
;extension=pdo_pgsql  →  extension=pdo_pgsql
;extension=pgsql      →  extension=pgsql
```

Para encontrar la ruta de tu `php.ini`:

```bash
php --ini
```

### 4. Configurar el entorno

```bash
cp .env.example .env
php artisan key:generate
```

Edita el `.env` con tus credenciales de PostgreSQL:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=db_cup_ficct
DB_USERNAME=postgres
DB_PASSWORD=tu_password
```

> Crea la base de datos `db_cup_ficct` en pgAdmin antes de continuar.

Agrega también las credenciales de Stripe (modo sandbox para desarrollo):

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
PAYMENT_CURRENCY=USD
```

### 5. Migraciones y seeders

```bash
php artisan migrate:fresh --seed
```

### 6. Levantar el servidor

```bash
php artisan serve
```

Abre **http://localhost:8000**

---

## Usuarios de prueba

| Usuario | Contraseña | Perfil |
|---|---|---|
| `admin_ficct` | `Admin123/*` | Administrador |
| `ana_admin` | `Admin2026` | Ventanilla |
| `evans_docente` | `DocenteFicct` | Docente |
| `sistema` | `cvpd2026` | Administrador |

---

## Reglas de negocio

- Aprobado ≥ 60 / Reprobado < 60
- Grupos = CEIL(Total inscritos / 70), máximo 70 estudiantes por grupo
- Docente: mínimo 1, máximo 4 grupos asignados, sin cruce de horarios
- Cupos por carrera: primera opción → si lleno, segunda opción → lista de espera
- Pasarela de pago obligatoria (no simulada)
- BD poblada con mínimo 3 gestiones académicas

---

## Pagos (CU-04)

La pasarela de pago usa **Stripe Elements** — el formulario de tarjeta vive embebido dentro del sistema (no hay redirección externa). El flujo es:

1. El postulante carga el formulario de pago → la vista pide un `client_secret` al servidor (`PagoController@iniciarPago`)
2. Stripe.js confirma el pago directamente en el navegador con los datos de la tarjeta
3. El frontend notifica al servidor (`PagoController@pagoExitoso`) para registrar el pago en BD
4. Como respaldo, un **webhook** (`PagoController@pagoWebhook`) escucha `payment_intent.succeeded` por si el paso 3 falla (ej. el usuario cierra el navegador)

---

## Arquitectura

- **Patrón:** MVC (Laravel) con principio *Fat Model / Skinny Controller* aplicado parcialmente
- **RBAC:** perfiles y privilegios granulares; `sistema.total` otorga acceso total y debe verificarse explícitamente en `Usuario::tienePrivilegio()`
- **Claves compuestas:** `grupo` y `grupo_materia` usan PK compuesta — Eloquent no las soporta bien, por eso esas tablas se manejan con `DB::table()` en vez de modelos Eloquent
- **Gestión activa:** se determina por el código más reciente (`SPLIT_PART`), nunca por un campo booleano

---

## Docker

El proyecto incluye un `Dockerfile` usado por Railway para producción (instala PHP 8.2, extensiones para PostgreSQL/PDF, y corre migraciones + servidor al desplegar).

> Para desarrollo local **no es necesario usar Docker** — `php artisan serve` contra tu PostgreSQL local es más simple y rápido para iterar. El Dockerfile congela el código en el momento del build, por lo que no refleja cambios en vivo salvo que se monte como volumen.

Para probar el Dockerfile localmente (opcional):

```bash
docker build -t sistema-cup .
docker run -p 8000:8000 -e PORT=8000 --env-file .env --add-host=host.docker.internal:host-gateway sistema-cup
```

> Si pruebas esto, recuerda volver `DB_HOST=127.0.0.1` en tu `.env` antes de usar `php artisan serve` de nuevo — `host.docker.internal` solo es válido dentro de un contenedor.

---

## Comandos útiles

```bash
# Reiniciar BD y resembrar
php artisan migrate:fresh --seed

# Ver todas las rutas
php artisan route:list

# Limpiar caché
php artisan config:clear && php artisan cache:clear && php artisan route:clear
```

---

## Notas

- El `.env` nunca se sube al repositorio — cada desarrollador crea el suyo desde `.env.example`
- En producción (Railway): `SESSION_SECURE_COOKIE=true`, `SESSION_SAME_SITE=lax` y `$middleware->trustProxies(at: '*')` en `bootstrap/app.php` son obligatorios para HTTPS y compatibilidad con dispositivos móviles
- Rama de producción: `master`
- Rama Victor: `victor` | Rama Pither: `pither`

---

## Estado del proyecto

Proyecto entregado y defendido como parte de la materia *Sistemas de Información I* (FICCT — UAGRM), bajo la metodología PUDS en 2 ciclos. Ambos ciclos completos, incluyendo pagos simulados en entorno de prueba con Stripe, RBAC granular, generación de reportes dinámicos (PDF/Excel) y despliegue continuo en Railway.