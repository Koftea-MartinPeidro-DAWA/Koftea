# Koftea
Es una tienda online, especializada en la venta de café y té de alta calidad. Ofrecemos una amplia selección de café en grano, molido y en cápsulas, abarcando diversas procedencias e intensidades para todos los gustos. En la sección de té, nos centramos en la venta exclusiva de hoja pura en variedades como verde, negro y oolong, priorizando su forma más natural. Nuestro objetivo es ofrecer un catálogo diverso y accesible para expertos y principiantes, fomentando una comunidad a través de reseñas y ayudando a descubrir la cultura y los matices de estas bebidas especiales.

## 🗺️ Índice de Contenidos

1.  [Ficheros Principales](#ficheros-principales)
2.  [Archivos de Configuración](#archivos-de-configuracion)
3.  [Documentación y Módulos](#documentacion-y-modulos)

---

## 1. Ficheros Principales

Aquí encontrarás enlaces directos a los ficheros críticos de la raíz del proyecto.

* **[Página Principal (index.html)](./index.html)**
    > *El punto de entrada principal del sitio web.*
* **[Fichero del Formulario (formulario.php)](./formulario.php)**
    > *Script PHP que gestiona la lógica del formulario.*
* **[Lógica del Formulario JS (src/form.js)](./src/form.js)**
    > *Manejo de eventos y validaciones del formulario en JavaScript.*
* **[Estilos Globales (src/style.css)](./src/style.css)**
    > *Hoja de estilos principal de la aplicación.*

---

## 2. Archivos de Configuración

Archivos necesarios para la configuración del entorno y dependencias.

* **[Docker Compose (docker-compose.yml)](./docker-compose.yml)**
    > *Define los servicios (PHP, Apache/Nginx) para levantar el entorno de desarrollo.*
* **[Dockerfile (Dockerfile)](./Dockerfile)**
    > *Instrucciones para construir la imagen de PHP/Servidor personalizada.*

### 🐳 Comandos de Docker

Para levantar y detener el entorno de desarrollo:

* **Iniciar el entorno (en segundo plano):**
    ```bash
    docker compose up -d
    ```
* **Detener y eliminar contenedores:**
    ```bash
    docker compose down
    ```

---

## 3. Documentación y Módulos

Archivos de documentación y carpetas de activos.

* **[Documentación Principal (README.md)](./README.md)**
    > *Descripción general y resumen del proyecto (¡este archivo!).*
* **[Riesgos y Seguridad (PrevencionRiegos.md)](./PrevencionRiegos.md)**
    > *Documento sobre las consideraciones de seguridad del proyecto.*
* **[Carpeta de Imágenes Globales (images/)](./images)**
    > *Contiene subcarpetas para `category`, `icons` y `products`.*