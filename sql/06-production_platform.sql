drop table if exists production_platform;
create table production_platform
(
    id_produkcji integer not null,
    id_platformy integer not null,
    dostepny_sezon integer,
    primary key (id_produkcji, id_platformy, dostepny_sezon),
    foreign key (id_produkcji) references productions (id),
    foreign key (id_platformy) references platforms (id)
);