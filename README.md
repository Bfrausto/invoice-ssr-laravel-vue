# Facturador Full-Stack (Prueba Técnica)

Una aplicación web completa para la creación y gestión de facturas, construida con Laravel, Inertia.js y Vue.js. El proyecto cumple con requisitos de SSR, performance, autenticación y despliegue en un entorno de producción.

## Instrucciones para Correr Localmente

Este proyecto está configurado para correr en un entorno de desarrollo local usando **Laravel Sail** (Docker).

### Requisitos Previos
* PHP (>= 8.2)
* Composer
* Node.js & NPM
* Docker & Docker Compose

### Pasos de Instalación

1.  **Clonar el Repositorio**
    ```bash
    git clone [https://github.com/tu-usuario/tu-repositorio.git](https://github.com/tu-usuario/tu-repositorio.git)
    cd tu-repositorio
    ```

2.  **Configurar el Entorno**
    Copie el archivo de ejemplo de variables de entorno y genere la clave de la aplicación.
    ```bash
    cp .env.example .env
    ```

3.  **Instalar Dependencias de PHP**
    Esto instalará Laravel y sus dependencias, incluyendo Sail.
    ```bash
    composer install
    ```

4.  **Levantar los Contenedores**
    Inicie los servicios de Docker (aplicación, base de datos) con Sail. La primera vez, esto descargará las imágenes y puede tardar varios minutos.
    ```bash
    ./vendor/bin/sail up -d
    ```

5.  **Generar la Clave de la Aplicación**
    ```bash
    ./vendor/bin/sail artisan key:generate
    ```

6.  **Instalar Dependencias de Frontend**
    ```bash
    ./vendor/bin/sail npm install
    ```

7.  **Ejecutar las Migraciones y Seeders**
    Esto creará la estructura de la base de datos y la llenará con datos de prueba.
    ```bash
    ./vendor/bin/sail artisan migrate:fresh --seed
    ```

8.  **Compilar Assets de Frontend**
    Ejecute el servidor de desarrollo de Vite, que compilará los assets y se recargará automáticamente al detectar cambios.
    ```bash
    ./vendor/bin/sail npm run dev
    ```

9.  **¡Listo!**
    La aplicación ahora debería estar corriendo en la URL definida en tu archivo `.env` (por defecto `http://localhost`).

---

## Variables de Entorno y Secrets

El archivo `.env` se utiliza para configurar las credenciales y parámetros específicos de tu entorno. **Nunca debe ser subido a un repositorio de Git.**

A continuación se describen las variables más importantes que debes configurar en tu archivo `.env` al copiarlo desde `.env.example`:

| Variable | Descripción | Ejemplo |
| :--- | :--- | :--- |
| `APP_NAME` | El nombre de tu aplicación. | `"Facturador"` |
| `APP_ENV` | El entorno de la aplicación. | `local` |
| `APP_KEY` | Clave de encriptación única. Se genera con `php artisan key:generate`. | `base64:...` |
| `APP_DEBUG` | Activa/desactiva el modo de depuración. | `true` |
| `APP_URL` | La URL base de tu aplicación local. | `http://localhost` |
| `DB_CONNECTION` | El driver de la base de datos. Sail lo configura por defecto. | `mysql` |
| `DB_HOST` | El host de la base de datos. Sail lo configura por defecto. | `mysql` |
| `DB_DATABASE` | El nombre de la base de datos. | `facturador` |
| `DB_USERNAME` | El usuario de la base de datos. | `sail` |
| `DB_PASSWORD` | La contraseña de la base de datos. | `password` |
| `VITE_PORT` | Puerto que Vite usa para el Hot Module Replacement (HMR). | `5173` |

---

## Decisiones de Arquitectura (ADR)

Para documentar las decisiones de arquitectura importantes que se tomaron durante el desarrollo, se utiliza un registro simple (ADR - Architectural Decision Record).

La decisión principal sobre el stack tecnológico y la arquitectura general del proyecto se encuentra documentada en:

* **[ADR-001: Elección del Stack Tecnológico](./docs/adr/ADR-001.md)**
