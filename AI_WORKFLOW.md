# AI Workflow Report

Este documento detalla cómo se utilizaron las herramientas de IA como asistente en el desarrollo de la prueba técnica, enfocándose en la resolución de problemas específicos, la generación de boilerplate y la exploración de patrones.

## Herramientas y Modelos Utilizados

* **Asistente Principal:** Google Gemini (versión más reciente a través de la interfaz web en Agosto de 2025). Utilizado para diálogos, depuración y generación de bloques de código.
* **Asistente en Editor:** GitHub Copilot (última versión para VS Code). Utilizado para autocompletado de código y sugerencias en línea.

## Proceso de Trabajo y Decisiones de Aceptación/Rechazo

La IA se utilizó como un compañero de programación ("pair programmer") para acelerar tareas, no para tomar decisiones de arquitectura. A continuación, se detallan decisiones clave:

### Decisiones de Aceptación

1.  **Generación de `Dockerfile` de Producción:**
    * **Sugerencia:** Se le pidió a Gemini un `Dockerfile` de producción para una aplicación Laravel con Nginx y PHP-FPM.
    * **Decisión:** **Aceptada con modificaciones.** La base inicial era buena, pero requirió múltiples iteraciones para resolver problemas de dependencias (`libpng`, `oniguruma`, `libpq-dev`) y errores de compilación (`tokenizer`). Verifiqué cada fallo con los logs de despliegue de Render y le pedí a la IA que ajustara el `Dockerfile` hasta que el build fue exitoso.

2.  **Lógica de `watcher` en Vue.js:**
    * **Sugerencia:** Para implementar la conversión de moneda automática, le pregunté a Gemini por el patrón correcto en Vue 3. Sugirió usar un `watch` sobre la propiedad `form.currency`.
    * **Decisión:** **Aceptada.** La lógica proporcionada era idiomática y correcta. La verifiqué implementándola y probando el cambio de moneda en el navegador, confirmando que los precios de los ítems se actualizaban correctamente según la tasa de cambio.

3.  **Boilerplate de Pruebas Unitarias:**
    * **Sugerencia:** Solicité la estructura de una prueba unitaria para un servicio de Laravel (`InvoiceServiceTest`). La IA generó el archivo con `use RefreshDatabase`, el método `setUp()` y un método de prueba de ejemplo siguiendo el patrón Arrange-Act-Assert.
    * **Decisión:** **Aceptada y adaptada.** Acepté la estructura base, pero adapté completamente la sección "Arrange" para que coincidiera con mi modelo de datos (incluyendo descuentos y estados) y escribí aserciones (`asserts`) específicas para verificar mis cálculos de negocio.

### Decisiones de Rechazo o Modificación Crítica

1.  **Manejo de Proxies en Laravel 11:**
    * **Sugerencia Inicial:** Para resolver un error de "Mixed Content" en producción, la IA sugirió modificar el middleware `TrustProxies.php`.
    * **Decisión:** **Rechazada y corregida.** Me di cuenta de que ese archivo no existe en una instalación limpia de Laravel 11. Le informé a la IA de la versión del framework, y me proporcionó la solución correcta y moderna: configurar los proxies en `bootstrap/app.php`. Esto fue un hallazgo clave sobre la dependencia del contexto en las sugerencias de la IA.

2.  **Lógica de Modales en el Formulario:**
    * **Sugerencia Inicial:** La IA proveyó la lógica para los modales de creación de clientes/compañías directamente dentro del componente principal `Form.vue`.
    * **Decisión:** **Rechazada por motivos de arquitectura.** Decidí que era un mal patrón (componente demasiado grande). Le pedí explícitamente a la IA que refactorizara esa lógica, extrayendo los modales a sus propios componentes reutilizables y comunicándose con el formulario principal a través de `props` y `emits`. La IA generó el código refactorizado, el cual revisé y acepté.

## Riesgos, Hallazgos y Mitigaciones

* **Hallazgo: Conocimiento Desactualizado.** El caso de `TrustProxies.php` demostró que los modelos pueden no estar al día con las últimas versiones de los frameworks.
    * **Mitigación:** Siempre proporcionar la versión exacta de las librerías en los prompts y verificar las soluciones contra la documentación oficial.

* **Riesgo: Pérdida de Contexto en Conversaciones Largas.** En una ocasión, al pedir simplificar el modal de **impuestos**, la IA proporcionó una simplificación para el modal de **compañías**.
    * **Mitigación:** Mi responsabilidad como desarrollador es mantener el contexto claro, re-afirmando el objetivo en el prompt ("Ahora, simplifica el modal de **impuestos** para que solo permita crear...").

* **Riesgo: Exceso de Confianza (Copiar/Pegar sin Comprender).**
    * **Mitigación:** Nunca acepté un bloque de código sin entender cada línea. El proceso de depuración del `Dockerfile` fue un claro ejemplo donde la comprensión del error (a través de los logs) era necesaria para guiar a la IA hacia la siguiente iteración correcta.
