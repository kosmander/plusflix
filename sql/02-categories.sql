create table categories
(
    id      integer not null
        constraint categories_pk
            primary key autoincrement,
    nazwa text not null
);