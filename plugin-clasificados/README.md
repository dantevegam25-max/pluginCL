# Plugin Clasificados SEO Silos

Un plugin de clasificados estilo Marketplace enfocado en escalabilidad SEO mediante estructura de silos. Desarrollado específicamente para WordPress y compatible de manera nativa con GeneratePress Pro.

## Características Principales (MVP)
*   **Custom Post Type "Anuncio":** Registra el tipo de contenido específico para los clasificados.
*   **Taxonomías Personalizadas:**
    *   `anuncio_categoria`: Para categorías como "Vehículos", "Mascotas", etc.
    *   `anuncio_ubicacion`: Jerárquica para representar País > Ciudad > Distrito.
*   **SEO Silos (Programático):** Genera dinámicamente URLs en el formato `/{categoria}/{ciudad}/{distrito}/` (ej. `/vehiculos/lima/san-borja/`) sin conflictos con las páginas nativas de WordPress.
*   **Gestión SEO Dinámica:** Genera etiquetas H1, Títulos (Title tags) y Meta Descripciones optimizadas y dinámicas basándose en la categoría y la ubicación de la URL actual.
*   **Plantillas Nativas Integradas:** Incluye plantillas para el archivo de anuncios (el silo) y la vista individual de anuncio (`single-anuncio.php`) preparadas para GeneratePress Pro.

## Guía de Instalación y Pruebas MVP

### 1. Activación
1.  Asegúrate de que la carpeta del plugin se llama `plugin-clasificados` y colócala en `/wp-content/plugins/`.
2.  Activa el plugin "Plugin Clasificados SEO Silos" desde el panel de WordPress. Al activarlo, las reglas de reescritura (rewrite rules) se refrescarán automáticamente.

### 2. Configuración de Taxonomías
Para probar la estructura `/vehiculos/lima/san-borja/`:

1.  Ve a **Anuncios > Categorías** y crea la categoría:
    *   **Nombre:** Vehículos
    *   **Slug:** vehiculos
2.  Ve a **Anuncios > Ubicaciones** y crea la jerarquía de ciudades y distritos:
    *   Crea un término padre llamado "Lima" (slug: `lima`).
    *   Crea un término hijo llamado "San Borja" (slug: `san-borja`) cuyo padre sea "Lima".

### 3. Crear un Anuncio de Prueba
1.  Ve a **Anuncios > Agregar Nuevo**.
2.  Crea un anuncio de prueba (ej. "Toyota Yaris 2020").
3.  Asigna la categoría "Vehículos".
4.  Asigna las ubicaciones "Lima" y "San Borja" (es recomendable asignar el término más específico, "San Borja", y asegurarte de que también está marcado el padre si tu tema lo requiere, aunque nuestro Query maneja el hijo).
5.  Publica el anuncio.

### 4. Verificar las URLs del Silo SEO
Visita las siguientes URLs en tu dominio para verificar el funcionamiento:

*   **Silo completo:** `dominio.com/vehiculos/lima/san-borja/` -> Debería cargar `archive-anuncios.php`, mostrar un H1 dinámico ("Vehículos en San Borja") y listar los anuncios que cumplan con estos criterios.
*   **Silo de ciudad:** `dominio.com/vehiculos/lima/` -> Debería mostrar los anuncios de "Vehículos" en todo "Lima".
*   **Silo de categoría:** `dominio.com/vehiculos/` -> Debería mostrar todos los vehículos. (Nota: WordPress puede redirigir esto a la taxonomía nativa si coincide con el slug `clasificados/vehiculos`, dependiendo de la prioridad, pero nuestro plugin intercepta esta URL base).
*   **Single Anuncio:** Haz clic en el anuncio listado para ver la vista individual (cargará `single-anuncio.php`).

*(Si alguna URL lanza un error 404, ve a Ajustes > Enlaces permanentes y haz clic en "Guardar cambios" para forzar un flush manual, o usa el menú "Clasificados" en el admin).*

---

## Escalabilidad Futura (Roadmap)

Este MVP está diseñado con una arquitectura robusta pensada para competir en SEO a gran escala.

### 1. Agregar nuevas categorías o ubicaciones masivas
*   **Sin tocar código:** Dado que las reglas de reescritura de `class-rewrite-rules.php` son genéricas (atrapan `^([^/]+)/([^/]+)/([^/]+)/?$`), añadir 50 nuevas categorías y 100 nuevas ciudades desde el panel de control de WordPress **no requiere modificar el código del plugin**. Simplemente creas los términos y el sistema de ruteo (`Template Loader` y `SEO Manager`) los interpretará dinámicamente verificando su existencia a través de `get_term_by()`.
*   **Importación Masiva:** Para escalar rápidamente, se puede integrar con plugins como *WP All Import* para cargar miles de términos de ubicación (País > Región > Ciudad > Distrito) y anuncios desde CSV/XML.

### 2. Generación dinámica de páginas (Sin inflar la base de datos)
*   **SEO Programático Real:** Las páginas como `/mascotas/arequipa/cayma/` **no son páginas físicas ni "Pages" en el wp_posts**. Son vistas generadas dinámicamente en tiempo de ejecución interceptando la petición HTTP a través de `WP_Query` basándose en las variables de URL. Esto previene que la base de datos colapse al tener millones de posibles combinaciones (Categoría X * Ciudad Y * Distrito Z).

### 3. Rendimiento y Caché a Gran Escala
*   **Uso Eficiente de WP_Query:** Actualmente el plugin hace un cruce básico usando `tax_query` con la relación `AND`. A gran escala, si la tabla de relaciones de términos se vuelve un cuello de botella, se puede extender la clase para usar tablas personalizadas o integrar un motor de búsqueda indexado como **ElasticPress (Elasticsearch)**.
*   **Caché Avanzada:** Las rutas dinámicas son totalmente compatibles con soluciones de caché como Redis/Memcached (Object Cache para las consultas de términos) y plugins de Full Page Cache (como WP Rocket o Litespeed Cache) ya que responden como URLs estándar (GET). Para escalar más, el `SEO_Manager` puede almacenar en caché (`set_transient`) los nombres generados de ciudades/categorías.

### 4. Integraciones (APIs / Scrapers)
*   El CPT `anuncio` ya se ha registrado con `'show_in_rest' => true`, lo que significa que de forma predeterminada está disponible en la **REST API de WordPress**.
*   Puedes conectar aplicaciones móviles externas, scripts de Python, o scrapers para insertar (POST) y leer (GET) anuncios directamente usando los endpoints nativos de WordPress (`/wp-json/wp/v2/anuncio`).
