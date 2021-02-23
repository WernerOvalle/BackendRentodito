/*DROP DATABASE api_rest_laravel;*/
CREATE DATABASE IF NOT EXISTS api_rest_laravel;
USE api_rest_laravel;
create TABLE Tiendas(
id              int(255) auto_increment not null,
name            varchar(100),
description     text,
estado varchar(255),
image           varchar(255),
created_at      datetime DEFAULT NULL,
updated_at      datetime DEFAULT NULL,

CONSTRAINT PK_TIENDAS PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE users(
id              int(255) auto_increment not null,
name            varchar(50) NOT NULL,
surname         varchar(100),
role            varchar(20),
tienda_id        int(255),
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
CONSTRAINT FK_USR_TIENDA FOREIGN KEY(tienda_id) REFERENCES Tiendas(id),
CONSTRAINT pk_users PRIMARY KEY(id)
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
user_id         int(255) ,
tienda_id     int(255) ,
cliente   varchar(255),
categoria_id     int(255) not null,
title           varchar(255) not null,/*0 ES tendero 1 es particular*/
apartado        boolean DEFAULT 0,
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
