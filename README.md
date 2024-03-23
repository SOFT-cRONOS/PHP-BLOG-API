# SOLAR
## English
### The software SOLAR:
The Blog Content Management System (SOLAR) is a program written in PHP using the RESTful API format. It is designed to handle CRUD operations and manage the content of websites, blogs, news, and more.
### Features:
* Public post with title, HTML content, sinop, tags, author and date.

### In progress.

* Login with tokens
* Password hashing in MySQL
* Security on private links
--------------------------------------------------------------------------------------------------------------
## Español
### El programa SOLAR:
El Sistema Online para la Administración de Redacciones (SOLAR) es un programa escrito en PHP que utiliza el formato de API RESTful. Está diseñado para manejar operaciones CRUD y administrar el contenido de sitios web, blogs, noticias y más.

### Estructura de archivos:


    api/
    │
    ├── index.php
    │
    ├── config/
    │   └── database.php
    │   └── auth.php
    │
    ├── models/
    │   ├── tagsClass.php
    │   └── postClass.php
    │   
    └── endpoints/
        ├── user.php
        └── product.php


    

### Funciones:
* Publicar escritos con Titulo, contenido HTML, sinopsis, etiquetas, autor y fecha de publicacion.


### En progreso.
* Login con tokens
* hash de password en mysql
* seguridad en enlaces privados

### Testeo en docker

Instalar imagen oficial de php-apache
* sudo docker run -d -p 80:80 --name 'nombre-contenedor' -v "$PWD":/var/www/html php:8-apache

Instalar mysqli

    //abrir terminal del contenedor
    docker exec -it webserver /bin/bash

    //instalar mysqli
    docker-php-ext-install mysqli

    //habilitar rewrite en Apache
    a2enmod rewrite

### Endpoints

* /post

    respuesta: 
    
[
  {
    "id": 0,
    "title": "",
    "sinopsis": "",
    "date": "",
    "image_url": "http://...",
    "nick": "",
    "categoria": ""
  },
]