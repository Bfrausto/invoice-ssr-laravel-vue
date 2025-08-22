## Prompt para Refactorizar Componente de Vue

> Tengo un componente de Vue (`Form.vue`) que contiene el código HTML y la lógica para un modal. Esto está haciendo que el componente principal crezca demasiado.
>
> Refactoriza este código. Extrae toda la lógica y el template del modal a un nuevo componente hijo llamado `CreateClientModal.vue`.
>
> El componente padre (`Form.vue`) debe controlar la visibilidad del modal con una prop `show`. El componente hijo (`CreateClientModal.vue`) debe emitir dos eventos al padre: un evento `@close` cuando se quiera cerrar, y un evento `@client-created` que envíe el objeto del nuevo cliente cuando se guarde con éxito.
