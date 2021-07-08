# API MUSICPRO

## Lider proyecto
 - Jorge Martínez F.
 
## Montar API Local

Paso 1   
tener un servidor que soporte PHP Version 7.4.3 como minimo y Mysql 5.7

Paso2
dentro de la carpeta HTDOCS (o la que se alojen los sitios)
pegar carpeta "api.musicpro" que se encuentra dentro de la carpeta "codigo_fuente"

Paso 3
Crear una base de datos Mysql con el nombre "musicpro" y cotejamiento "utf8_general_ci"
Importar base de datos con archivo “db_api_musicpro.sql”

Paso 4
Agregar datos de conexión en archivo /catalogo/v1/conexion/config

Paso 5
Las URL que aparecen en la documentación son de la nube por ende para poder interactuar con la API se deberá cambiar url por http://localhost/api.musicpro/ 

Paso 6
La colección de postman (API_MusiPro.postman_collection.json) está apuntando a la nube https://api.musicpro.hexagram.cl/ y también se debe cambiar la url por http://localhost/api.musicpro/

Paso 7
Las pruebas realizadas se encuentran https://api.musicpro.hexagram.cl/pruebas/

Paso 8
Repositorio GIT “https://github.com/jmartifi/api.musicpro” V1.0.0


 
