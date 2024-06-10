# Proyecto de Revisión de Código


## Tecnologías

* Lenguaje de programación: **PHP 8.1**
* Framework: **Laravel 10**
* Base de datos: **MySQL**
* Integraciones:
    * **Gemini API** [google-gemini-php/laravel](https://github.com/google-gemini-php/laravel)
    * **OpenAI API** [openai-php/laravel](https://github.com/openai-php/laravel)


## Base de datos

![Diagrama Entidad-Relación](public/docs/erd.png)

Vaciar la base de datos usando el siguiente comando que eliminará todas las tablas y volverá a ejecutar todas las migraciones:

```
php artisan migrate:fresh --seed
```

Después de las migraciones, los valores predefinidos en la clase `DatabaseSeeder` de la carpeta `database/seeders` son insertados en la base de datos.


## Configuración General

Antes de empezar a subir y analizar proyectos, asegurar la configuración de los parámetros como `GEMINI_API_KEY`, `OPENAI_API_KEY`, `OPENAI_ORGANIZATION`, etc., para el uso de las API de IA.

![](public/docs/captura_001.png)


## Configuración de Usuarios

Solo los usuarios con el rol de administrador pueden crear nuevos usuarios, asignar rol de administrador o pentester al usuario, y borrarlos.

Crear un nuevo usuario mediante el formulario y copiar la contraseña para dárselo al usuario.

![](public/docs/captura_002.png)

Por defecto, un nuevo usuario se crea con el rol de `Pentester`, se puede cambiar el rol haciendo clic en el botón `Pentester` o `Administrador`.

![](public/docs/captura_003.png)

Cuando el usuario inicie sesión con su cuenta, puede cambiar su contraseña en `Mi Perfil`.

![](public/docs/captura_004.png)


## Gestión de Proyectos

Subir un proyecto con extensión `.zip`, el tamaño del archivo debe ser menor a 256MB.

![](public/docs/captura_005.png)

El proyecto subido es almacenado y descomprimido en la carpeta `storage/app/projects`. Una vez finalice la subida del proyecto, se procede a descomprimir automáticamente los archivos filtrando las extensiones registradas.

La opción para borrar el proyecto se encuentra al final de la página del listado de archivos. La operación está permitida solo para el usuario administrador o el usuario que ha subido el proyecto.

![](public/docs/captura_006.png)


## Análisis de Código

Ingresar a la página de análisis seleccionando el proyecto a analizar.

![](public/docs/captura_007.png)

Iniciar con el botón `Analizar`, si no están configurados los API keys, la página se redirigirá a la de `Configuración General`.

![](public/docs/captura_008.png)

Cuando el análisis inicie, las tareas son creadas y ejecutadas en el segundo plano, la página se actualiza automáticamente con los resultados logrados.
![](public/docs/captura_009.png)

En el panel lateral se puede visualizar el progreso de las tareas del segundo plano en tiempo real.
![](public/docs/captura_010.png)
