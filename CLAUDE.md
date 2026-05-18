# Sistema de Gestión de Casos - Sistema Silvina

## Stack
- Laravel 11 + Blade + Bootstrap 5
- MySQL
- Maatwebsite/Excel para exportar Excel
- DomPDF para exportar PDF
- Laravel Breeze para autenticación

## Estructura
- `casos` → tabla principal de expedientes
- `localidades` → lista dinámica de localidades
- `tipos_expediente` → Nota, Memorandum, Oficio Judicial, etc.

users
├── id, name, email, password, timestamps

casos
├── id
├── fecha_recepcion          (date)
├── nro_legajo               (string) → "2125/26"
├── nro_expediente           (string) → "S-000932/26"
├── tipo_expediente_id       (FK → tipos_expediente)
├── apellido_nombre          (string)
├── dni                      (string)
├── localidad_id             (FK → localidades)
├── barrio                   (string, nullable)
├── telefono                 (string, nullable)
├── denunciado               (string, nullable)
├── resumen                  (text, nullable)
├── acepta_atencion          (boolean) → Sí/No
├── servicio_legal           (boolean)
├── servicio_psicologico     (boolean)
├── servicio_social          (boolean)
├── archivado                (boolean) → Sí/No
├── observaciones            (text, nullable)
├── fecha_devolucion         (date, nullable)
└── timestamps

tipos_expediente
├── id
└── nombre   → "Nota", "Memorandum", "Oficio Judicial", etc.

localidades
├── id
└── nombre   → "Clorinda", "Colorado", etc.

## MODULOS
/login                      → Acceso al sistema
/dashboard                  → Resumen general (totales, últimos casos)
/casos                      → Listado con filtros y búsqueda
/casos/create               → Nuevo caso
/casos/{id}                 → Ver detalle
/casos/{id}/edit            → Editar
/casos/export/excel         → Descarga Excel
/casos/export/pdf           → Descarga PDF

/localidades                → ABM de localidades (agregar desde el sistema)
/tipos-expediente           → ABM de tipos (por si agrega más)

## Convenciones
- En español (variables, nombres de rutas, todo)
- Validaciones server-side en FormRequests
- Paginación en listados (15 por página)

