/*DROP DATABASE api_rest_laravel;*/
CREATE DATABASE IF NOT EXISTS api_rest_laravel;
USE api_rest_laravel;

create TABLE users(
id              int(255) auto_increment not null,
name            varchar(50) NOT NULL,
surname         varchar(100),
role            varchar(20),
email           varchar(255) NOT NULL,
telefono           varchar(255) NOT NULL,
password        varchar(255) NOT NULL,
ine1           varchar(255),
punteo         varchar(255),
ine2           varchar(255),
image           varchar(255),
created_at      datetime DEFAULT NULL,
updated_at      datetime DEFAULT NULL,
remember_token  varchar(255),
CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE Tiendas(
id              int(255) auto_increment not null,
name            varchar(100),
description     text,
image           varchar(255),
created_at      datetime DEFAULT NULL,
updated_at      datetime DEFAULT NULL,
CONSTRAINT PK_TIENDAS PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE Categorias(
id              int(255) auto_increment not null,
name            varchar(100),
description     text,
created_at      datetime DEFAULT NULL,
updated_at      datetime DEFAULT NULL,
CONSTRAINT PK_CATEGORIA PRIMARY KEY(id)
)ENGINE=InnoDb;

/*
CREATE TABLE Personas(
id              int(255) auto_increment not null,
name            varchar(100),
description     text,
created_at      datetime DEFAULT NULL,
updated_at      datetime DEFAULT NULL,
CONSTRAINT PK_PERSONAS PRIMARY KEY(id)
)ENGINE=InnoDb;

*/
create TABLE Articulos(
id              int(255) auto_increment not null,
user_id         int(255) not null,
tienda_id     int(255) not null,
categoria_id     int(255) not null,
title           varchar(255) not null,
apartado        boolean,
fecha_apartado  datetime,
content         text not null,
image           varchar(255),
created_at      datetime DEFAULT NULL,
updated_at      datetime DEFAULT NULL,
CONSTRAINT PK_ARTICULOS PRIMARY KEY(id),
CONSTRAINT FK_ART_USER FOREIGN KEY(user_id) REFERENCES users(id),
CONSTRAINT FK_ART_TIEND FOREIGN KEY(tienda_id) REFERENCES Tiendas(id),
CONSTRAINT FK_ART_CATE FOREIGN KEY(categoria_id) REFERENCES Categorias(id)
)ENGINE=InnoDb;


CREATE TABLE Servicios(
id              int(255) auto_increment not null,
user_id         int(255) not null,
title           varchar(255) not null,
content         text not null,
image           varchar(255),
created_at      datetime DEFAULT NULL,
updated_at      datetime DEFAULT NULL,
CONSTRAINT PK_SERV PRIMARY KEY(id),
CONSTRAINT FK_SERV_USER FOREIGN KEY(user_id) REFERENCES users(id)

)ENGINE=InnoDb;
