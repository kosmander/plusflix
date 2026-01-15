create table platforms
(
    id      integer not null
        constraint platforms_pk
            primary key autoincrement,
    nazwa text not null,
    logo_url text not null,
    platform_url text not null
);
