# CUP FICCT — Sistema de Gestión de Admisión Preuniversitaria

## Stack
- PHP 8.2, Laravel, PostgreSQL, Blade, HTML5/CSS3/JavaScript
- Deploy: https://sistema-web-cup-production.up.railway.app (Railway + PostgreSQL)
- Repo: https://github.com/pithersec/Sistema-Web-CUP

## Equipo
- Pither Daniel Condori Villanueva (Reg. 224027085)
- Victor David Sosa Coca (Reg. 223044431)
- Ambos participan en documentación UML, diagramas, diseño de BD e implementación
- Trabajamos en ramas separadas: `victor` y `pither`, con merge a `master`
- Hacer `git pull origin victor` (o `pither`) antes de empezar cada sesión
- Commitear seguido con mensajes claros
- Mergear a `master` después de cada funcionalidad estable

## Metodología
- PUDS en 2 ciclos
- Ciclo 1: ≥50% funcional — entrega P1 sábado 30/05/2026 23:59
- Ciclo 2: 100% completo — entrega P2 sábado 13/06/2026 23:59
- Defensa: lunes 15/06/2026 7:00 am

## Actores
- **Postulante**: se registra vía formulario público, no tiene cuenta en el sistema
- **Docente**: accede con credenciales, registra notas de su grupo/materia
- **Administrador**: accede con credenciales, gestiona todo el sistema

## Casos de uso
| ID | Nombre | Ciclo | Prioridad |
|---|---|---|---|
| CU-01 | Iniciar sesión | 1 | Alta |
| CU-02 | Cerrar sesión | 1 | Alta |
| CU-03 | Realizar preinscripción | 1 | Alta |
| CU-04 | Realizar pago | 2 | Media |
| CU-05 | Consultar estado de admisión | 2 | Media |
| CU-06 | Presentar reclamo | 2 | Media |
| CU-07 | Registrar notas | 1 | Alta |
| CU-08 | Consultar rendimiento académico | 2 | Media |
| CU-09 | Consultar indicadores estadísticos | 1 | Alta |
| CU-10 | Generar reportes | 2 | Media |
| CU-11 | Generar asignación de grupos | 2 | Alta |
| CU-12 | Atender reclamos | 2 | Media |
| CU-13 | Gestionar postulantes | 1 | Alta |
| CU-14 | Gestionar personal | 1 | Alta |
| CU-15 | Gestionar carreras y cupos | 1 | Alta |
| CU-16 | Gestionar usuarios y perfiles | 1 | Alta |
| CU-17 | Cargar cuentas masivas | 2 | Media |
| CU-18 | Configurar parámetros del sistema | 2 | Baja |
| CU-19 | Consultar bitácora de auditoría | 1 | Media |
| CU-20 | Gestionar asistencia | 2 | Media |

## Paquetes
| Paquete | CU incluidos |
|---|---|
| P1 Gestión de Admisión | CU-03, CU-04, CU-05, CU-06 |
| P2 Gestión Académica | CU-07, CU-08, CU-11, CU-12, CU-20 |
| P3 Gestión Administrativa | CU-13, CU-14, CU-15, CU-16, CU-17, CU-18 |
| P4 Seguridad y Reportes | CU-01, CU-02, CU-09, CU-10, CU-19 |

Dependencias entre paquetes: P1→P3, P2→P3, P2→P4, P3→P4

## Reglas de negocio clave
- Nota final por materia = (N1 × 30%) + (N2 × 30%) + (N3 × 40%) — ponderación configurable
- Aprobado ≥ 60 / Reprobado < 60
- Grupos = CEIL(Total inscritos / 70)
- Máximo 70 estudiantes por grupo
- Docente: mínimo 1, máximo 4 grupos asignados
- Sin cruce de horarios entre grupos del mismo docente
- Cupos por carrera: primera opción → si lleno, segunda opción → si lleno, lista de espera
- Pasarela de pago: Stripe (sandbox para presentación).
- BD poblada con al menos 3 gestiones académicas (seeders). Actualmente hay 4 gestiones.
- Notas de solo lectura para el docente una vez registradas.

## Diagrama de clases — entidades principales
| Entidad | Atributos clave |
|---|---|
| `datos_personales` | ci (PK), nombre, apellido, genero, telefono, correo, fecha_nac, direccion |
| `postulante` | codigo (PK), procedencia, telefono_2, plazo, estado, gestion_egreso, ci (FK), id_requisitos_postulante (FK), id_colegio (FK), id_pago (FK), id_grupo (FK), gestion_grupo (FK compuesta con id_grupo → grupo), estado_formulario, nombre_turno (FK → turno), created_at |
| `personal` | registro (PK), estado, ci (FK → datos_personales) |
| `requisitos_personal` | id (PK), registro_personal (FK → personal), area, nivel_grado, nivel_exp, maestria, doctorado, diplomado |
| `usuario` | id (PK), user_name, clave, email, id_perfil (FK), registro_personal (FK) |
| `perfil` | id (PK), nombre, descripcion |
| `privilegio` | id (PK), nombre |
| `perfil_privilegio` | id_perfil (FK), id_privilegio (FK) — tabla intermedia |
| `bitacora` | id (PK), ip, accion, fecha_hora, id_usuario (FK) |
| `carrera` | codigo (PK), plan, nombre, modalidad, nivel, tipo, duracion |
| `carrera_gestion` | codigo_carrera (FK), codigo_gestion (FK), cupos — PK compuesta |
| `gestion` | codigo (PK), fecha_ini, fecha_fin |
| `colegio` | id (PK), cie, nombre, tipo, turno, pais, departamento, provincia |
| `requisitos_postulante` | id (PK), titulo_original, titulo_copia, fotocopia_carnet, formulario, comprobante, libreta |
| `pago` | id (PK), monto, fecha, concepto, estado, id_transaccion, moneda |
| `reclamo` | id (PK), descripcion, fecha, dirigido, estado, codigo_postulante (FK), registro_personal (FK) |
| `materia` | id (PK), nombre, duracion |
| `grupo` | id + codigo_gestion (PK compuesta), aula, total_ins, codigo_gestion (FK → gestion), nombre_turno (FK → turno) |
| `grupo_materia` | id_materia (FK), id_grupo (FK), gestion_grupo (FK compuesta con id_grupo → grupo), hora_inicio, hora_fin, orden, registro_personal (FK) — PK compuesta (id_materia, id_grupo, gestion_grupo) |
| `examen` | codigo_postulante (FK), nro_examen, id_materia (FK) — PK compuesta, ponderacion, nota, fecha |
| `turno` | nombre (PK), hora_inicio, hora_fin |
| `asistencia` | fecha+codigo_postulante+codigo_gestion+id_grupo+id_materia (PK compuesta), presente |
| `correccion` | id (PK), nota_anterior, nota_nueva, justificacion, fecha, registro_personal (FK), nro_examen (FK), codigo_postulante (FK), id_materia (FK) |

## RBAC
- Perfiles definidos en tabla `perfil` 
- Privilegios en tabla `privilegio` 
- Tabla intermedia `perfil_privilegio`
- Usuario tiene exactamente 1 perfil (FK `id_perfil` en `usuario`)
- Un perfil tiene N privilegios a través de `perfil_privilegio`

## Herencia (mapeo tabla por clase)
- `datos_personales` es la clase base con CI como PK
- `postulante` y `personal` tienen su propia tabla con FK → `datos_personales`
- Una persona puede ser postulante Y personal al mismo tiempo (no disjunto)

### Completado P1 ✅
- Perfil completo (introducción, objetivos, problema, alcance)
- Marco Teórico
- Modelo de negocio: 4 diagramas de actividad (StarUML)
- FT Captura de Requisitos: actores, CU, priorización, detalles CU ciclo 1, modelo CU ciclo 1
- FT Análisis: identificar paquetes, relacionar paquetes-CU, vista de CU por paquete, análisis de paquete
- Análisis de un caso de uso — diagramas de comunicación ciclo 1
- Análisis de una clase — ciclo 1 (CU-01, CU-02, CU-03, CU-07, CU-09, CU-13, CU-14, CU-15, CU-16, CU-19)
- Diagrama de clases (StarUML)
- Mapeo lógico (Excel)
- DDL PostgreSQL (SQL script)
- FT Diseño: arquitectura física (diagrama de despliegue), lógica (diagrama de paquetes en capas)
- Diagramas de secuencia ciclo 1 (los 10 CU)
- Diseño de datos físico en documento
- Prototipos de interfaz ciclo 1 (10 pantallas)
- Conclusión y recomendación

### Completado P2 ✅
- Tabla de priorización de casos de uso ciclo 2
- Todos los detalles de caso de uso
- Estructura de modelo ciclo 2
- Todos los diagramas de comunicación y análisis de clase
- CU-04, CU-05, , CU-06, CU-08, CU-10, CU-11, CU-12, CU-17, CU-20 ya está todo completo en código y documentación
- CU-04: ya implementado con pasarela de pago Stripe en entorno de prueba
- CU-08 implementado completo (rendimiento académico con vista index y detalle)
- CU-17 implementado con carga .xlsx, .xls y .csv
- CU-10 ya genera reportes dinámicos formato pdf excel
- Pasarela Stripe con webhook configurado en Railway
- Dashboard separado por perfil (admin/sistema vs docente)
- Módulo de privilegios funcional con RBAC
- FT Implementación: herramientas, arquitectura sistema/subsistema (diagramas de componentes)

### Discusión y decisiones de diseño pendientes

**CU-21: Corregir Nota (pendiente de documentar e implementar)**
- Las notas ya registradas son de solo lectura para el docente — no puede modificarlas directamente
- Para corregir una nota se necesita un proceso formal: el postulante presenta un reclamo, el admin lo atiende y si procede corrige la nota
- Requiere una nueva tabla `correccion` con: id (PK), codigo_postulante + id_materia + nro_examen (FK compuesta → examen), nota_anterior, nota_nueva, justificacion, registro_personal (FK → personal), fecha (TIMESTAMP)
- Se conecta con `personal` y no con `usuario` porque el responsable académico es la persona, no su cuenta
- El admin también puede corregir sin reclamo previo (error detectado directamente) — en ese caso `id_reclamo` sería nullable
- CU-21 es un CU simple de un solo flujo lineal, tanto en documentación como en implementación

**notas.editar vs notas.registrar**
- `notas.registrar` → privilegio del docente, solo permite ingresar notas nuevas (campos vacíos)
- `notas.editar` → privilegio exclusivo del administrador, vinculado a CU-21
- Implementación de `notas.editar` queda pendiente hasta que CU-21 esté documentado

**ExamenService (refactor pendiente)**
- La lógica de `procesarExamenes` está duplicada en `PostulanteController` y `RendimientoController`
- Refactorizar a `app/Services/ExamenService.php` después de la presentación

**Dashboard docente**
- Máximo 4 grupos por docente en producción real (seeders actuales tienen más por pruebas)
- CU-08 rendimiento académico accesible desde el sidebar para admin y docente
- El promedio general de materias no se muestra — para aprobar se evalúa cada materia individualmente

## Paquetes instalados
- `maatwebsite/excel` — para exportación Excel
- `barryvdh/laravel-dompdf` — para exportación PDF

## Convenciones de documentación — Fichas de Caso de Uso
- Propósito y Descripción: nunca mencionar CU específicos por número, solo lenguaje natural
- Flujo principal: pasos numerados simples (1. 2. 3.) sin subniveles — para acciones con bifurcaciones usar negrita inline (Si registra nuevo:, Si edita:, Si da de baja:)
- Pre/Postcondición: "Ninguna" si no aplica — no rellenar por rellenar
- Pre/Postcondición: solo mencionar CU si la relación es directa e importante
- Excepción: "Ninguna" si no aplica

## Convenciones de documentación — Diagramas de Caso de Uso
- Include: solo si la acción es obligatoria y parte del flujo directo
- Extend: solo si la acción es opcional
- Include es una precondición, extend es una postcondición
- No graficar CU-01 como include en todos — es obvio
- Solo agregar relaciones si son muy necesarias o directas

## Convenciones de documentación — Diagramas de Comunicación
- Estructura: Actor → Vista → Controller → Entidad(es)
- Símbolos: Actor (monigote), Vista (interfaz/boundary), Controller (círculo con flecha), Entidad (círculo simple)
- Formato de mensajes: una cadena por acción del actor: 1, 1.1, 1.2, 1.3 / 2, 2.1, 2.2...
- Sin mensajes de retorno, solo de ida
- Paréntesis siempre vacíos: listarPostulantes(), no listarPostulantes(ci)
- El Controller se conecta directo a cada entidad; las entidades no se relacionan entre sí
- Cada cadena llega hasta todas las entidades que esa acción necesita consultar/modificar
- Las entidades corresponden a tablas/modelos del diagrama de clases
- Agregar entidades adicionales solo si esa acción consulta o modifica datos de otra tabla
- Los mensajes piensan en CRUD: 1 crear, 2 ver/listar, 3 modificar, 4 eliminar — no siempre aplican los 4
- El nombre del actor en el diagrama debe coincidir con el actor iniciador definido en la ficha del CU
- Debajo de cada diagrama va un resumen en texto (2-4 oraciones) describiendo el flujo del caso de uso en lenguaje natural

## Convenciones de documentación — Análisis de Clase

- Los métodos del análisis de clase deben coincidir con los mensajes del diagrama de comunicación
- Los métodos del controller en código Laravel deben reflejar esos mismos nombres
- Estructura: Actor → Vista → Controller → Entidad principal → Entidades secundarias
- Vista: atributos = campos visibles/llenables en pantalla (sin tipos de dato, sin id internos, sin clave); métodos = acciones que el actor dispara
- Controller: sin atributos; métodos = exactamente los mensajes del diagrama de comunicación
- Entidad principal: la tabla que el CU modifica o consulta directamente; el controller apunta a ella
- Entidades secundarias: se relacionan con la entidad principal reflejando las FK del diagrama de clases, no con el controller directamente
- Bitácora siempre se relaciona con la entidad principal del CU, no con el controller
- Sin tipos de dato en atributos
- Sin getters/setters ni métodos técnicos de implementación

## Convenciones de documentación — Diagrama de Secuencia

- Lifelines: Actor, Vista, Controller, Entidades (mismos participantes que análisis de clase)
- Todos los lifelines usan el mismo símbolo: rectángulo arriba + línea punteada abajo
- Solo mensajes de ida (flechas sólidas), sin mensajes de retorno punteados
- Numeración lineal global y secuencial (1, 2, 3, 4...)
- Los mensajes deben coincidir con los del diagrama de comunicación (mismos nombres)
- CORRECCIÓN PARA CICLO 2: los mensajes deben basarse en el análisis de clase, no en el diagrama de comunicación — el controller llama solo a la entidad principal, las entidades secundarias se comunican entre sí (pero para el número de mensajes si usamos el de comunicación)
- Barras de activación opcionales (la ingeniería no es rigurosa con eso)
- Fragmentos combinados: usar `alt` para flujos alternativos, con guardas entre corchetes [condición]
- Para CUs simples: 1 diagrama por CU
- Para CUs de gestión (CRUD): 1 diagrama con alt para cada acción (registrar, editar, dar de baja)
- El flujo de listar va fuera del alt como flujo principal
- Etiqueta del contenedor: `interaction NombreCU`

## Migraciones Laravel — notas

- Tabla `usuario` (no `users`) — renombrada para consistencia con el modelo
- `personal` tiene campo `estado boolean default true` agregado
- Enums usados: turno ['mañana','tarde','noche'], tipo colegio ['fiscal','particular','convenio','privado'], modalidad carrera ['presencial','virtual'], nivel carrera ['licenciatura','tecnico_superior','tecnico_medio'], tipo carrera ['semestral','anual'], estado pago ['pendiente','completado','rechazado','anulado'], estado_formulario ['activo','vencido','anulado'], estado_reclamo ['pendiente','atendido','rechazado'], area_docente ['matematicas','fisica','computacion','ingles','administracion','sistemas','otra'], nivel_grado_docente ['tecnico_medio','tecnico_superior','licenciatura','ingenieria','maestria','doctorado']
- `duracion` en carrera es `unsignedTinyInteger`
- `total_ins` en grupo es `unsignedSmallInteger`
- `cupos` en carrera_gestion es `unsignedSmallInteger`
- Orden de migraciones importante: usuario va después de perfil y personal por FK
- Conectado a PostgreSQL local para desarrollo, Railway PostgreSQL para producción
- grupo tiene PK compuesta (id, codigo_gestion) — reinicia contadores por gestión
- postulante y grupo_materia tienen columna gestion_grupo como parte de FK compuesta hacia grupo
- Secuencias PostgreSQL se resetean al final del PostulanteSeeder con setval

## Convenciones de respuesta
- Respuestas cortas y directas
- No reescribir archivos completos — solo edits puntuales
- Sin preámbulo extenso ni narración del plan
- Debatir proactivamente cuando algo importa
- Commitear por funcionalidad, no todo junto
- Recordar hacer git pull al inicio de cada sesión de trabajo