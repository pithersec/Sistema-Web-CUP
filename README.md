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
| **Ciclo 2** | CU-04, CU-05, CU-06, CU-08, CU-10, CU-11, CU-12, CU-17, CU-18 | ⏳ En desarrollo |

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
- Rama de producción: `master`
- Rama Victor: `victor` | Rama Pither: `pither`