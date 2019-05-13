drop database if exists database_test;

create database database_test;
  use database_test;

  drop table if exists user;
  create table user (
    id varchar(40) not null primary key,
    firstname varchar(50),
    lastname varchar(150),
    username varchar(64),
    email varchar(255),
    password char(60),
    role tinyint,
    status tinyint,
    date_inserted timestamp default current_timestamp(),
    date_updated datetime default null
  );

  drop table if exists accessToken;
  create table accessToken (
    id varchar(40) not null primary key,
    date_inserted timestamp default current_timestamp()
  ) ENGINE = MYISAM;

  -- simulate a given accessToken for the user 1
  insert into accessToken values ('8a56580369c1b1532b7638f10a8abce0545311d8', null);

  /* Triggers to update the date_updated after update */
  drop trigger if exists user_date_updated;
  delimiter //
  create trigger user_date_updated
    before update
    on user
    for each row
    begin
      set new.date_updated = current_timestamp();
    end //
    delimiter ;

drop table if exists echangescli;
create table echangescli (
	row_id varchar(50),
	row_idfixe varchar(50),
	dtecreation date,
	opecreation varchar(20),
	opemodif varchar(20),
	dtemodif date,
	hremodif varchar(10),
	ams char(1),
	actif char(1),
	codsit char(1),
	num_echange bigint,
	lib_echange text,
	cod_tiers varchar(50),
	typ_piece varchar(3),
	cod_piece bigint,
	nbr_msg_nonlu_tie tinyint,
	nbr_msg_nonlu_soc tinyint,
	solder char(1),
	solder_date date,
	solder_heure varchar(10),
	solder_codope_tiers varchar(50),
	solder_codope_soc varchar(50),
	primary key (row_idfixe)
) ENGINE=MYISAM;

create index codsit_echagescli_row_id on echangescli (codsit, row_id);

  drop table if exists pieces_dl;
  create table pieces_dl (
  	row_id varchar(50),
  	row_idfixe varchar(50),
  	dtecreation date,
  	opecreation varchar(20),
  	opemodif varchar(20),
  	dtemodif date,
  	hremodif varchar(10),
  	ams char(1),
  	actif char(1),
  	codsit char(1),
    cod_piece bigint,
  	typ_piece varchar(3),
    lu_tie char(1),
    date_dl date,
    heure_dl varchar(10),
  	primary key (row_idfixe)
  ) ENGINE=MYISAM;

  create index codsit_pieces_dl_row_id on echangescli (codsit, row_id);
