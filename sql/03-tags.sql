create table tags
(
    id      integer not null
        constraint tags_pk
            primary key autoincrement,
    nazwa text not null
);