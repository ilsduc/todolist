drop database if exists api;
create database api;
  use api;

  drop table if exists todo;
  create table todo (
    id varchar(60) primary key,
    content text,
    done boolean default false,
    date_inserted timestamp default current_timestamp()
  );
