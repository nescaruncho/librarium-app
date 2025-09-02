# Proxecto de fin de ciclo DAW

## Librarium - Gestor de bibliotecas

## Descrición

*Librarium* é unha aplicación web deseñada para aportar unha solución flexible e sinxela a usuarios que precisen dun sistema de xestión das súas bibliotecas (persoais e colectivas). O stack tecnolóxico empregado para o seu desenvolvemento é *Laravel 12*, *Vue 3*, *Tailwind CSS 4*, *Inertia.js*. A combinación destas tecnoloxías permite desenvolver un backend robusto e seguro cunha interfaz dinámica e responsiva, que é precisamente o enfoque principal desta aplicación

Cada usuario pode crear unha conta e personalizar o seu perfil. Unha vez autenticado, pode explorar as bibliotecas existentes a través de barra de búsqueda, ou crear a súa propia. As bibliotecas teñen unha configuración básica de visibilidade (pública ou privada), segundo a cal os usuarios externos poden unirse directamente ou solicitar unirse, e contan con distribución de responsabilidades por roles (lector, admin e owner), limitando o acceso a determinadas funcións segundo o mesmo.

O punto forte da aplicación reside no fluxo de gardado de libros, xa que permite engadir exemplares a unha biblioteca a partir do ISBN, integrándose coa API de [Open Library](https://openlibrary.org/dev/docs/api/books) para completar automaticamente todos os datos necesarios (título, autores, xéneros, editoriais...). A aplicación inclúe un sistema configurable de etiquetas, con exportación a PDF a través de [DOMPDF](https://github.com/barryvdh/laravel-dompdf) e xestión de ubicación de exemplares.

A maiores, a aplicación ofrece ós usuarios historial de lecturas e lecturas en curso. As búsquedas na aplicación son dinámicas (live search), tanto no catálogo dunha biblioteca coma no catálogo personal de lecturas. Existe un sistema interno de notificacións e está preparada para incorporar funcionalidades sociais nun futuro.

Esta aplicación constuise seguindo un patrón MVC monolítico, pero modular, con validacións a través de Form Request, controladores RESTful e migracións optimizadas en MariaDB. Está adaptada para ser despregada hosts que acepten conterización con Docker. O deseño UI/UX fíxose empregando Figma, priorizando a accesibilidade, a usabilidade e a experiencia de usuario.

## ⚙️ Requisitos

- [Docker](https://www.docker.com/) e [Docker Compose](https://docs.docker.com/compose/)
- [Git](https://git-scm.com/)
- (Opcional) [WSL](https://learn.microsoft.com/es-es/windows/wsl/install) (só se traballas en Windows)
- (Opcional) [Docker Desktop](https://www.docker.com/products/docker-desktop/)

> **Nota**: Este proxecto está construído con [Laravel Sail](https://laravel.com/docs/sail), e inclúe xa **Laravel + Breeze + Inertia + Vue 3 + Tailwind CSS** preconfigurados.  
> **Non é necesario instalar Vue nin Laravel manualmente.**

---

## 🚀 Instalación

1. **Clonar o repositorio**

   ```bash
   git clone https://gitlab.com/teu_usuario/librarium.git
   cd librarium
   ```

2. **Copiar o ficheiro de entorno**

   ```bash
   cp .env.example .env
   ```

3. **Instalación de dependencias desde o contenedor**

   ```bash
   docker run --rm \
      -u "$(id -u):$(id -g)" \
      -v "$PWD":/var/www/html \
      -w /var/www/html \
      laravelsail/php84-composer:latest \
      composer install --no-interaction --prefer-dist --optimize-autoloader
   ```

4. **Levantamento dos contedores**

   ```bash
   ./vendor/bin/sail up -d
   ```

5. **Instalación de dependencias**

   ```bash
   ./vendor/bin/sail npm install
   ```

6. **Compilación dos assets**

   ```bash
   ./vendor/bin/sail npm run build
   ```

7. **Xeración da clave da aplicación**

   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

8. **Execución das migracións**

   ```bash
   ./vendor/bin/sail artisan migrate
   ```

9. **Acceso á aplicación**

   💻Levanta o FrontEnd con Vite:

   ```bash
   ./vendor/bin/sail npm run dev
   
   ```

   Abre o navegador en:  
   👉 [http://localhost](http://localhost)

   Introduce as credenciais de proba (ou podes crear un novo usuario):
   *Email*: <admin@example.com>
   *Password*: admin123

---

## Uso

**Librarium** é unha aplicación web intuitiva que permite xestionar bibliotecas persoais ou compartidas dende calquera dispositivo. A través dunha interface limpa e responsive, podes:

- Crear e organizar bibliotecas con diferentes niveis de acceso.
- Engadir exemplares escaneando o ISBN e completando datos de forma automática.
- Buscar libros e filtralos en tempo real mentres escribes.
- Xestionar etiquetas únicas, dispoñibilidade e ubicación dos exemplares.
- Crear listas de lectura e levar un control dos libros lidos e pendentes.

Todo o control nun só lugar, sen complicacións e cun deseño pensado para que te concentres no máis importante: **os teus libros**.

## Sobre el autor

O meu nome é Andrés Caruncho e son desenvolvedor web, con enfoque no área de backend utilizando Java (SpringBoot) e PHP (Laravel), aínda que tamén teño experiencia desenvolvendo frontend con Vue, Bootstrap e Tailwind CSS.

Estou finalizando o Grao Superior en Desenvolvemento de Aplicacións Web e realicei prácticas nunha importante consultora tecnolóxica especializada na administración pública, onde tiven a oportunidade de experimentar como é o traballo nun entorno real, empregando ferramentas de control de versións (Git e SVN), documentación e despregamento de aplicacións. Tamén son graduado en Comunicación Audiovisual e teño experiencia traballando en departamentos de márketing realizando maquetación web, deseño de productos e xestión de redes sociais. Apaixóame o mundo narrativo en todas as súas formas (cine, libros, videoxogos), pero especialmente a creación de universos transmedia, que permiten contar historias aproveitando o potencial de diferentes medios.

A motivación para crear este proxecto vén da miña propia familia: miña nai contaba cunha biblioteca xigantesca, chea dunha enorme variadade de libros de diferentes xéneros e autores, contando con volumes antigos. O problema sempre foi a orde, xa que o espazo era limitado, e para iso usaba libretas nas que ía anotando cada libro e onde estaba. Cada dous por tres cambiaba a forma de ordealos, pero sempre o deixaba a medias, abrumada pola cantidade de libros. Un día, vendo o caos daqueles estantes, decidinme a facer unha aplicación que fose moi sinxela de usar e centrada en permitir a usuarios individuais ou pequenos grupos (como familias, asociacións ou colexios) contar cun sistema que lles permitise xestionar os seus libros. O mesmo sistema que á miña nai lle tería quitado moitas dores de cabeza.

A ela está dedicado este traballo.

## Contacto

Se tes algún comentario, queixa ou suxestión podes contactarme a través do meu correo persoal. Tamén podes ver outros proxectos feitos por min no meu GitHub.

📧 **Email:** [nescaruncho@gmail.com](mailto:nescaruncho@gmail.com)
💼 **GitHub:** [github.com/nescaruncho](https://github.com/nescaruncho)

## Licencia

Este proxecto está baixo a licencia **GNU General Public License v3.0**.
Isto significa que podes executar, estudar, compartir e modificar o software, sempre que manteñas a mesma licencia nas versións derivadas.

Consulta o ficheiro [LICENSE](./LICENSE) para máis información.

## Guía de contribución

As contribucións son benvidas para mellorar **Librarium**. Podes axudar de diferentes maneiras:

- Engadindo novas funcionalidades ou mellorando as existentes.
- Corrixindo erros ou refactorizando o código.
- Creando ou mellorando probas automatizadas.
- Propoñendo novas interfaces ou integracións con servizos externos.
- Mellorando a documentación e a tradución da aplicación.

**Procedemento para contribuír:**

1. Fai un *fork* deste repositorio.

2. Crea unha rama nova para a túa achega:

   ```bash
   git checkout -b feature/nome-da-tua-funcionalidade
   ```

3. Realiza os cambios e fai *commit* cunha mensaxe clara e que describa o que fixeches.

4. Envía unha *pull request* explicando brevemente a túa achega.

Antes de enviar a túa contribución, comproba que:

- O código segue as boas prácticas e estándares do proxecto.

- Pasaches todos os tests e a aplicación compila sen erros.

📬 Para dúbidas ou propostas, podes abrir unha issue en GitLab.

## Memoria

1. [Estudo preliminar](doc/templates/1_estudo_preliminar.md)
2. [Análise: Requerimentos do sistema](doc/templates/2_analise.md)
3. [Deseño](doc/templates/3_deseno.md)
4. [Codificación e Probas](doc/templates/4_codificacion_probas.md)
5. [Manuais](doc/templates/5_manuais.md)
6. [Defensa](doc/templates/6_defensa_do_proxecto.md)

### Anexos

1. [Referencias](doc/templates/a1_referencias.md)
2. [Planificación](doc/templates/a2_planificacion.md)
3. [Orzamento](doc/templates/a3_orzamento.md)
