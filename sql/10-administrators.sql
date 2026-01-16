-- tabela administratorów do logowania
drop table if exists administrators;
create table administrators
(
    id       integer not null
        constraint administrators_pk
            primary key autoincrement,
    login    text not null unique,
    password text not null,
    email    text
);

-- domyslny admin (haslo: admin123)
-- haslo zahashowane przez password_hash()
insert into administrators (login, password, email)
values ('admin', '$2y$12$tk9Sg0xBHPSf9CSuzNygoedy32VNZEhMa8DIZkG5zV.ZWZISApykG', 'admin@plusflix.pl');
