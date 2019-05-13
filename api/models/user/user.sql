/*
  ** Basic structure for a user
*/
drop table if exists user;
create table user (
  id char(40) primary key,
  firstname varchar(50),
  lastname varchar(150),
  email varchar(255),
  pwd char(60),
  emailVerified boolean default false,
  token char(40),
  role tinyint,
  status tinyint,
  date_inserted timestamp default current_timestamp(),
  date_updated datetime default null
) ENGINE=MYISAM;

/*
  ** trigger for updating "date_updated" field
*/
drop trigger if exists _before_update_user_set_date_update;
delimiter //
create trigger _before_update_user_set_date_update
  before update on user
  for each row begin
    set new.date_updated = current_timestamp();
  end //
delimiter ;

/*
  ** code for adding media to user
*/
-- adding media field
alter table user
  add column id_media char(40);
-- adding foreign key constraint
alter table user
  add foreign (id_media) references media(id);
