# Copilot Instructions for SIGSIH

## Arquitectura General
- Proyecto basado en Laravel (PHP), siguiendo la estructura estándar de Laravel: `app/`, `config/`, `routes/`, `resources/`, `database/`, `public/`, `tests/`.
- Los modelos se encuentran en `app/Models/`, los controladores en `app/Http/Controllers/`, y los comandos de consola en `app/Console/Commands/`.
- El sistema utiliza migraciones (`database/migrations/`) y seeders (`database/seeders/`) para la gestión de la base de datos.
- El frontend utiliza Vite y Tailwind CSS, con archivos de configuración en la raíz (`vite.config.js`, `tailwind.config.js`).

## Flujos de Desarrollo
- **Compilar frontend:** Usar Vite (`npm run dev` para desarrollo, `npm run build` para producción).
- **Ejecutar servidor local:** Usar `php artisan serve`.
- **Migraciones:** `php artisan migrate` para aplicar cambios en la base de datos.
- **Semillas:** `php artisan db:seed` para poblar datos iniciales.
- **Pruebas:** Ejecutar `php artisan test` o `vendor/bin/phpunit`.

## Convenciones y Patrones
- Los controladores siguen el patrón RESTful.
- Los modelos Eloquent se ubican en `app/Models/` y representan tablas de la base de datos.
- Los recursos de Livewire están en `app/Livewire/`.
- Helpers personalizados en `app/Helpers/`.
- Las rutas principales están en `routes/web.php` (web) y `routes/api.php` (API).
- Las vistas Blade se encuentran en `resources/views/`.

## Integraciones y Dependencias
- Utiliza Docker (`Dockerfile`, `docker-compose.yml`) para entornos de desarrollo y despliegue.
- Integración con JWT (`config/jwt.php`) y Laravel Sanctum (`config/sanctum.php`) para autenticación.
- Tailwind y PostCSS para estilos (`postcss.config.js`).
- Livewire para componentes interactivos.

## Ejemplo de Estructura de Código
- Modelo: `app/Models/Factura.php`
- Controlador: `app/Http/Controllers/FacturaController.php`
- Vista: `resources/views/facturas/index.blade.php`
- Migración: `database/migrations/xxxx_xx_xx_create_facturas_table.php`

## Recomendaciones para Agentes AI
- Priorizar convenciones de Laravel y respetar la estructura de carpetas.
- Al crear nuevos componentes, seguir la ubicación y nomenclatura de los existentes.
- Consultar archivos de configuración para integraciones y dependencias.
- Validar cambios ejecutando pruebas y migraciones.

---
¿Hay algún aspecto del flujo de trabajo, integración o convención que requiera mayor detalle o aclaración?