# Librarium - Gestor de bibliotecas

## Descripción

*Librarium* es una aplicación web diseñada para aportar una solución flexible y sencilla a usuarios que necesiten un sistema de gestión de sus bibliotecas (personales y colectivas). El stack tecnológico empleado para su desarrollo es *Laravel 12*, *Vue 3*, *Tailwind CSS 4*, *Inertia.js*. La combinación de estas tecnologías permite desarrollar un backend robusto y seguro con una interfaz dinámica y responsiva, que es precisamente el enfoque principal de esta aplicación.

Cada usuario puede crear una cuenta y personalizar su perfil. Una vez autenticado, puede explorar las bibliotecas existentes a través de una barra de búsqueda, o crear la suya propia. Las bibliotecas tienen una configuración básica de visibilidad (pública o privada), según la cual los usuarios externos pueden unirse directamente o solicitar unirse, y cuentan con distribución de responsabilidades por roles (lector, admin y propietario), limitando el acceso a determinadas funciones según el rol.

El punto fuerte de la aplicación reside en el flujo de guardado de libros, ya que permite añadir ejemplares a una biblioteca a partir del ISBN, integrándose con la API de [Open Library](https://openlibrary.org/dev/docs/api/books) para completar automáticamente todos los datos necesarios (título, autores, géneros, editoriales...). La aplicación incluye un sistema configurable de etiquetas, con exportación a PDF a través de [DOMPDF](https://github.com/barryvdh/laravel-dompdf) y gestión de ubicación de ejemplares.

Además, la aplicación ofrece a los usuarios historial de lecturas y lecturas en curso. Las búsquedas en la aplicación son dinámicas (live search), tanto en el catálogo de una biblioteca como en el catálogo personal de lecturas. Existe un sistema interno de notificaciones y está preparada para incorporar funcionalidades sociales en un futuro.

Esta aplicación se construyó siguiendo un patrón MVC monolítico, pero modular, con validaciones a través de Form Request, controladores RESTful y migraciones optimizadas en MariaDB. Está adaptada para ser desplegada en hosts que acepten contenerización con Docker. El diseño UI/UX se realizó usando Figma, priorizando la accesibilidad, la usabilidad y la experiencia de usuario.

## ⚙️ Requisitos

- [Docker](https://www.docker.com/) y [Docker Compose](https://docs.docker.com/compose/)
- [Git](https://git-scm.com/)
- (Opcional) [WSL](https://learn.microsoft.com/es-es/windows/wsl/install) (solo si trabajas en Windows)
- (Opcional) [Docker Desktop](https://www.docker.com/products/docker-desktop/)

> **Nota**: Este proyecto está construido con [Laravel Sail](https://laravel.com/docs/sail), e incluye ya **Laravel + Breeze + Inertia + Vue 3 + Tailwind CSS** preconfigurados.  
> **No es necesario instalar Vue ni Laravel manualmente.**

---

## 🚀 Instalación

1. **Clonar el repositorio**

   ```bash
   git clone https://gitlab.com/teu_usuario/librarium.git
   cd librarium
   ```

2. **Copiar el archivo de entorno**

   ```bash
   cp .env.example .env
   ```

3. **Instalación de dependencias desde el contenedor**

   ```bash
   docker run --rm \
      -u "$(id -u):$(id -g)" \
      -v "$PWD":/var/www/html \
      -w /var/www/html \
      laravelsail/php84-composer:latest \
      composer install --no-interaction --prefer-dist --optimize-autoloader
   ```

4. **Levantamiento de los contenedores**

   ```bash
   ./vendor/bin/sail up -d
   ```

5. **Instalación de dependencias**

   ```bash
   ./vendor/bin/sail npm install
   ```

6. **Compilación de los assets**

   ```bash
   ./vendor/bin/sail npm run build
   ```

7. **Generación de la clave de la aplicación**

   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

8. **Ejecución de las migraciones**

   ```bash
   ./vendor/bin/sail artisan migrate
   ```

9. **Acceso a la aplicación**

   💻Levanta el FrontEnd con Vite:

   ```bash
   ./vendor/bin/sail npm run dev
   
   ```

   Abre el navegador en:  
   👉 [http://localhost](http://localhost)

## Uso

**Librarium** es una aplicación web intuitiva que permite gestionar bibliotecas personales o compartidas desde cualquier dispositivo. A través de una interfaz limpia y responsive, puedes:

- Crear y organizar bibliotecas con diferentes niveles de acceso.
- Añadir ejemplares escaneando el ISBN y completando datos de forma automática.
- Buscar libros y filtrarlos en tiempo real mientras escribes.
- Gestionar etiquetas únicas, disponibilidad y ubicación de los ejemplares.
- Crear listas de lectura y llevar un control de los libros leídos y pendientes.

Todo el control en un solo lugar, sin complicaciones y con un diseño pensado para que te concentres en lo más importante: **tus libros**.

## Sobre el autor

Mi nombre es Andrés Caruncho y soy desarrollador web, con enfoque en el área de backend utilizando Java (SpringBoot) y PHP (Laravel), aunque también tengo experiencia desarrollando frontend con Vue, Bootstrap y Tailwind CSS.

Estoy finalizando el Grado Superior en Desarrollo de Aplicaciones Web y realicé prácticas en una importante consultora tecnológica especializada en la administración pública, donde tuve la oportunidad de experimentar cómo es el trabajo en un entorno real, empleando herramientas de control de versiones (Git y SVN), documentación y despliegue de aplicaciones. También soy graduado en Comunicación Audiovisual y tengo experiencia trabajando en departamentos de marketing realizando maquetación web, diseño de productos y gestión de redes sociales. Me apasiona el mundo narrativo en todas sus formas (cine, libros, videojuegos), pero especialmente la creación de universos transmedia, que permiten contar historias aprovechando el potencial de diferentes medios.

La motivación para crear este proyecto viene de una necesidad personal. Un día, buscando un libro en la caótica librería que tenemos en casa, me decidí a hacer una aplicación que fuese muy sencilla de usar y centrada en permitir a usuarios individuales o pequeños grupos (como familias, asociaciones o colegios) contar con un sistema que les permitiese gestionar sus libros.

## Contacto

Si tienes algún comentario, queja o sugerencia puedes contactarme a través de mi correo personal. También puedes ver otros proyectos hechos por mí en mi GitHub.

📧 **Email:** [nescaruncho@gmail.com](mailto:nescaruncho@gmail.com)  
💼 **GitHub:** [github.com/nescaruncho](https://github.com/nescaruncho)

## Licencia

Este proyecto está bajo la licencia **GNU General Public License v3.0**.  
Esto significa que puedes ejecutar, estudiar, compartir y modificar el software, siempre que mantengas la misma licencia en las versiones derivadas.

Consulta el archivo [LICENSE](./LICENSE) para más información.

## Guía de contribución

Las contribuciones son bienvenidas para mejorar **Librarium**. Puedes ayudar de diferentes maneras:

- Añadiendo nuevas funcionalidades o mejorando las existentes.
- Corrigiendo errores o refactorizando el código.
- Creando o mejorando pruebas automatizadas.
- Proponiendo nuevas interfaces o integraciones con servicios externos.
- Mejorando la documentación y la traducción de la aplicación.

**Procedimiento para contribuir:**

1. Haz un *fork* de este repositorio.

2. Crea una nueva rama para tu aportación:

   ```bash
   git checkout -b feature/nombre-de-tu-funcionalidad
   ```

3. Realiza los cambios y haz un *commit* con un mensaje clare que describa lo que hiciste.

4. Envía una *pull request* explicando brevemente tu aportación.

Antes de enviar tu contribución, comprueba que:

- El código sigue las buenas prácticas y estándares del proyecto.
- Pasaste todos los tests y la aplicación compila sin errores.

📬 Para dudas o propuestas, puedes abrir una issue en GitHub
