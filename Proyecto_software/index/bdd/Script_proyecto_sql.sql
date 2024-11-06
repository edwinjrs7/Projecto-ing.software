create schema proyecto_bd;

create table usuario (
    id int(11) auto_increment primary key,
    username char(150) not null,
    firstname char(30) not null,
    lastname char(150) not null,
    email char(254) not null,
    contraseña char(128) not null,
    tipo_usuario ENUM('usuario', 'empresa') NOT NULL
);

create table consumo(
   id int(11) auto_increment primary key,
   user_id int(11),
   consumo float(8) not null,
   constraint
   foreign key (user_id) references usuario(id)
);

create table consumoMensual(
    id  int(11) auto_increment primary key,
	id_consumo int(11),
    costo decimal(65) not null,
    periodo char(150) not null,
    constraint
    foreign key (id_consumo) references consumo(id)
);

create table consumoAnual(
     id int(11) auto_increment primary key,
     id_consumo int(11),
     total_pagado float,
     total_consumido float(50), 
     año YEAR,
	 constraint
     foreign key (id_consumo) references consumo(id)
);

create table empresa(
    id int(11) auto_increment primary key,
    nombre varchar(150) not null,
    tipo varchar(150) not null,
    ubicacion varchar(100) not null,
    descripcion text(500) not null,
    sector varchar(50) not null,
    logo varchar(255),
    sitio_web varchar(255),
    calificacion_promedio varchar(255)
    

);

create table seleccionarEmpresa(
    id int(11) auto_increment primary key not null,
    id_usuario int(11) not null,
    id_empresa int(11) not null,
    constraint
    foreign key (id_usuario) references usuario(id),
    foreign key (id_empresa) references empresa(id)
);

insert into empresa(id,nombre,tipo,ubicacion,descripcion,sector,logo,sitio_web,calificacion_promedio) values (1, 'AguaSafe', 'privada', 'Barranquilla, Atlantico', 'Aguasafe es una empresa líder en la generación de energía hídrica, comprometida con la sostenibilidad y la protección del medio ambiente. Con una visión innovadora y responsable, aprovechamos el poder del agua para generar energía limpia que impulsa el desarrollo y mejora la calidad de vida de las comunidades a las que servimos.','energia hidraulica', '../images/agua.png','www.aguaSafe.com',5);
insert into empresa(nombre,tipo,ubicacion,descripcion,sector,logo,sitio_web,calificacion_promedio) values ('Solar Panel Inc', 'privada', 'Sabanalarga, Atlantico', 'Solar Panel Inc ayuda a los hogares y empresas a reducir significativamente su dependencia de fuentes de energía tradicionales, logrando una reducción promedio del 40% al 60% en el consumo de energía. Sus productos combinan tecnología de punta y materiales duraderos para maximizar la captación solar y convertirla en energía limpia y asequible, contribuyendo así a un futuro más verde y económico para sus clientes.','energia hidraulica', '../images/solar.png','www.SolarPanelINC.com',5);
delete from empresa where id = 1;
select * from usuario