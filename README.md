====================================================
           PRESTASHOP DOCKER INSTALLATION
====================================================

Descripción:
Este repositorio implementa PrestaShop 8.2 con Docker, usando una carpeta local
para persistir los archivos (ubicada en "app/html") y aprovechando el contenedor de 
MySQL ya existente (del stack de Laravel). Se utiliza Traefik como reverse proxy para 
enrutar el dominio "prestashop.francobg.online" al contenedor de PrestaShop.

Requisitos Previos:
-------------------
1. Tener Docker y Docker Compose instalados.
2. Contenedor MySQL (del stack de Laravel) funcionando con:
     - MYSQL_ROOT_PASSWORD: secret
     - MYSQL_DATABASE: laravel (para Laravel) y se debe crear la base de datos "prestashop"
     - MYSQL_USER: laravel
     - MYSQL_PASSWORD: secret
3. El usuario "laravel" debe tener todos los privilegios sobre la base de datos "prestashop".
4. Se debe haber configurado la red externa "webnet" y ambos stacks (Laravel y PrestaShop)
   deben estar conectados a ella.
5. DNS: El dominio "prestashop.francobg.online" debe apuntar a la IP de este VPS.
6. (Opcional) Traefik ya debe estar corriendo en el stack de Laravel para enrutar las peticiones.

Instalación:
------------
1. En el servidor, crea el directorio del proyecto:
   $ sudo mkdir -p /var/www/francobg-prestashop
   $ cd /var/www/francobg-prestashop

2. Crea el subdirectorio "app" para almacenar los archivos de PrestaShop:
   $ mkdir app

3. Clona el repositorio (o coloca aquí el contenido del proyecto):
   (Ejemplo: git clone <URL_del_repositorio> .)

4. Asegúrate de que el archivo "docker-compose.yml" esté configurado de la siguiente forma:

-----------------------------------------------------------
version: '3.8'

services:
  prestashop_app:
    image: prestashop/prestashop:8.2
    container_name: prestashop_app
    restart: always
    environment:
      DB_SERVER: mysql_db             # Nombre del contenedor MySQL (del stack de Laravel)
      DB_NAME: prestashop             # Nombre de la base de datos para PrestaShop
      DB_USER: laravel                # Usuario con privilegios sobre la DB "prestashop"
      DB_PASS: secret
      # PS_INSTALL_AUTO: "1"         # (Opcional) Instalación automática; se recomienda desactivarla para
                                    # realizar la instalación manual y obtener mensajes de error claros.
      PS_LANGUAGE: es
      PS_COUNTRY: ES
    volumes:
      - ./app/html:/var/www/html     # Monta la carpeta local que contendrá los archivos de PrestaShop
    labels:
      - "traefik.enable=true"
      - "traefik.http.routers.prestashop.rule=Host(`prestashop.francobg.online`)"
      - "traefik.http.routers.prestashop.entrypoints=web"
      - "traefik.http.routers.prestashop-secure.rule=Host(`prestashop.francobg.online`)"
      - "traefik.http.routers.prestashop-secure.entrypoints=websecure"
      - "traefik.http.routers.prestashop-secure.tls=true"
      - "traefik.http.routers.prestashop-secure.tls.certresolver=myresolver"
    networks:
      - webnet

networks:
  webnet:
    external: true
-----------------------------------------------------------

5. (Importante) Si la variable PS_INSTALL_AUTO está activada y la instalación 
   automática no progresa, coméntala (añade un '#' al inicio de la línea) para 
   forzar una instalación manual:
      # PS_INSTALL_AUTO: "1"

6. Levanta el stack de PrestaShop:
   $ docker compose up -d --build

7. Accede manualmente al instalador:
   Abre en tu navegador:
      http://prestashop.francobg.online/install
   y sigue los pasos del instalador. Usa los siguientes datos para la conexión a la base de datos:

     - Servidor: mysql_db
     - Base de Datos: prestashop
     - Usuario: laravel
     - Contraseña: secret
     - Puerto: 3306 (por defecto)

8. Completa la instalación del instalador de PrestaShop.

9. Una vez finalizada la instalación, el contenido de PrestaShop se persistirá en la
   carpeta local "app/html", y Traefik enrutarà las peticiones a través del dominio
      prestashop.francobg.online

Solución de Problemas:
----------------------
- Verifica los logs del contenedor de PrestaShop:
      docker compose logs -f prestashop_app
- Asegúrate de que la base de datos "prestashop" exista y que el usuario "laravel"
  tenga los privilegios necesarios.
- Confirma que ambos stacks (Laravel y Prestashop) estén en la red "webnet".
- Si hay problemas con la instalación automática, prueba desactivándola y accediendo
  manualmente al instalador.

Notas Adicionales:
------------------
- Los archivos de PrestaShop se almacenan en el host en: /var/www/francobg-prestashop/app/html
- Traefik se usa como reverse proxy, y debe estar configurado en el otro stack (Laravel).
- Para reiniciar el stack, utiliza:
      docker compose down
      docker compose up -d --build

====================================================
              FIN DEL README
====================================================

