drop table if exists ratings;
create table ratings
(
    id      integer not null
        constraint ratings_pk
            primary key autoincrement,
    id_produkcji integer not null,
    ocena integer not null check (ocena between 1 and 5),
    tresc text,
    data text not null,
    status_moderacji text not null check (status_moderacji in ('oczekujące', 'zatwierdzone', 'odrzucone')),
    foreign key (id_produkcji) references productions (id)
);