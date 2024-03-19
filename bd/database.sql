-- Inicia sesión en MySQL como usuario root o un usuario con privilegios de administrador
CREATE DATABASE paecblog CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Crea un nuevo usuario y establece la contraseña
CREATE USER 'paecadmin'@'localhost' IDENTIFIED BY 'password';

-- Otorga todos los permisos al usuario sobre la base de datos
GRANT ALL PRIVILEGES ON paecblog.* TO 'paecadmin'@'localhost';

-- Actualiza los privilegios para que los cambios tengan efecto
FLUSH PRIVILEGES;

-- Creacion de tablas
USE paecblog;

CREATE TABLE empresa (
    id_empresa INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nombre varchar(30) NOT NULL,
    abreviatura varchar(5), 
    mail varchar(50),
    telefono varchar(50),
    direccion varchar(50),
    numero varchar(50)
);

INSERT INTO empresa (nombre, abreviatura, mail, telefono, direccion, numero) VALUES
("soft-cronos", "SC", "soft-cronos@gmail.com", "2222222", "sven nation army", "222");

CREATE TABLE autor (
    id_autor INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nick varchar(30) NOT NULL,
    nombre varchar(50),
    apellido varchar(50),
    mail varchar(50),
    profesion varchar(50)
)
;

CREATE TABLE categorias(
    id_categoria INT(3) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nombre varchar(30) NOT NULL,
    detalle varchar(100)
)
;

CREATE TABLE post (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  id_categoria int NOT NULL,
  id_autor int NOT NULL,
  title varchar(200) NOT NULL,
  sinopsis varchar(800) NOT NULL,
  content TEXT NOT NULL,
  date_create date NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  image_url varchar(255) DEFAULT NULL,
  tags varchar(100),
  valoration int,
  publishing_status boolean,
  foreign key (id_categoria) references categorias(id_categoria),
  foreign key (id_autor) references autor(id_autor)
);

CREATE TABLE tags (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    UNIQUE KEY unique_tag (name)
);

CREATE TABLE post_tags (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    tag_id INT NOT NULL,
    FOREIGN KEY (post_id) REFERENCES post(id),
    FOREIGN KEY (tag_id) REFERENCES tags(id)
);

CREATE TABLE visitantes (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    token varchar(25) NOT NULL,
    fecha DATE
);

-- index para enlazar token con token 
CREATE INDEX idx_token ON visitantes (token);

CREATE TABLE historial (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    fecha DATE,
    navegador varchar(50),
    token varchar(25) NOT NULL,
    os varchar(50),
    link varchar(200),
    FOREIGN KEY (token) REFERENCES visitantes(token)
);

CREATE TABLE medios (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    fecha DATE,
    nombre varchar(50),
    detalle varchar(50),
    link varchar(200)
);

CREATE TABLE visit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_post INT,
    visit_datetime DATETIME,
    FOREIGN KEY (id_post) REFERENCES post(id)
);


INSERT INTO autor (nick, nombre, apellido, mail, profesion) VALUES
("cRONOS", "Nicolas", "Donato", "softcronos@gmail.com", "informatico");

INSERT INTO categorias (nombre, detalle) VALUES
('informatica', 'todo sobre reparacion, software y mas'),
('ofimatica', 'procesadores de texto, planilla de calculo, precentaciones y mas'),
('software', 'programas gratis para descargar');

