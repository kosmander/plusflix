drop table if exists productions;
create table productions
(
    id      integer not null
        constraint productions_pk
            primary key autoincrement,
    tytul      text not null,
    opis       text not null,
    typ        text not null check (typ in ('film', 'serial')),
    rok        integer not null,
    kraj       text,
    plakat_url text not null
);