# Sistema de Gestión de Citas Médicas

Esta aplicación web permite la gestión de citas médicas para pacientes, así como la gestión de usuarios, médicos y especialidades por parte del Administrador del Sistema.

A continuación, se describen los pasos para configurar y ejecutar la aplicación localmente.

## Requisitos previos

Antes de comenzar, asegúrate de tener instalados los siguientes programas:

- Git (https://www.git-scm.com/downloads)
- XAMPP (https://www.apachefriends.org/es/download.html)
- Composer (https://getcomposer.org/download/)
- Node.js y npm (https://nodejs.org/en/download/package-manager)
- Symfony (https://symfony.com/download)

## Clona el Repositorio

Clona el repositorio desde GitHub:
    git clone https://github.com/Maruve8/Sistema_citas.git

## Instalación de dependencias

- Ve a tu directorio:
    cd citas_medicas

- Instala las dependencias de PHP usando Composer:
    composer install

- Instala las dependencias de Node.js usando npm:
    npm install

## Configuración de Base de Datos

- Creación de la base de datos
    Abre el panel de XAMPP y ejecuta los servicios de Apache y MySQL. Accede a phpMyAdmin (http://localhost/phpmyadmin) y crea una nueva base de datos citas_medicas.

- Archivo .env
    En el archivo .env local, la configuración debe aparecer: 
    DATABASE_URL="mysql://root:@127.0.0.1:3306/CITAS_MEDICAS?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
    No olvides modificar tu usuario y contraseña en caso necesario.

- Migraciones
    Ejecuta las migraciones para crear las tablas necesarias en la base de datos:
        php bin/console doctrine:migrations:migrate
    
    Si tienes problemas en la migración, puedes usar el siguiente comando:
        php bin/console doctrine:schema:update --force
    
- Importación de la base de datos
    Descarga la base de datos, la cual incluye las especialidades, médicos, un usuario con    ROL_USER, y un usuario con ROL_ADMIN. 
        [Descargar Base de Datos](https://drive.google.com/file/d/14Nnd-yK89fs05obpWAPC1TE8Z69_7uef/view?usp=drive_link)
    
    Ve a phpMyAdmin y selecciona la base de datos que creaste. Haz click en "importar", selecciona el archivo citas_medicas.sql y haz click en Ejecutar.

## Compila los activos (CSS, Javascript) con Symfony Encore:

    npm run dev


## Ejecuta la aplicación

    symfony server:start

La aplicación debe estar disponible en http://127.0.0.1:8000 o, en su defecto, donde te indique tu consola de comandos.


## Ejecución del Worker para el envío de emails

Symfony Messenger se utiliza para manejar el envío de emails en segundo plano. Debes ejecutar el worker que procesa la cola de mensajes cuando la aplicación se encuentra en etapa de desarrollo:

    php bin/console messenger:consume async -vv

## Automatización de tareas en Windows para cambiar el estado de las citas

Para utilizar el cambio de estado de las citas de "Confirmada" a "Finalizada" una vez han tenido lugar, puedes ejecutar un comando manualmente, haciéndolo de vez en cuando para asegurar que los cambios de estado tienen lugar, o puedes automatizar este proceso. Si prefieres la primera opción, el comando es:
    php bin/console app:actualizar-citas-finalizadas

En caso de automatizar la tarea, puedes seguir los siguientes pasos.

- Crea un script para ejecutar el comando
    - Abre un editor de texto.
    - Crea un nuevo archvivo llamado actualizar_citas.bat.
    - Añade el siguiente contenido a este archivo:
        @echo off
        cd citas_medicas
        php bin/console app:actualizar-citas-finalizadas

        (Asegúrate de cambiar la ruta si es distinta.)
    - Guarda el archivo.

- Configura el Programador de Tareas de Windows
    - Accede al Programador de Tareas
    - Selecciona "Crear Tarea"
    - Dale un nombre a la tarea y establece la seguridad en "Ejecutar con los privilegios más altos"
    - En Desencadenadores haz click en "Nuevo", y configura la frecuencia deseada de ejecución. Por ejemplo, cada 30 minutos y repetir diariamente.
    - En Acciones, haz click en "Nuevo..", y en "Programa/script" selecciona el archivo .bat que has creado.
    - Guarda la tarea en "Aceptar"

## Acceso al Sistema

- Usuario paciente: mv.contrerasbellido@gmail.com / 123456
- Usuario administrador: citas.medicas.contacto@gmail.com / Admin123
    


    


    
