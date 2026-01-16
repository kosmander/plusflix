drop table if exists tags;
create table tags
(
    id      integer not null
        constraint tags_pk
            primary key autoincrement,
    nazwa text not null
);