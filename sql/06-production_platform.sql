create table production_platform
(
    id_produkcji integer not null,
    id_platformy integer not null,
    dostępny_sezon integer,
    primary key (id_produkcji, id_platformy),
    foreign key (id_produkcji) references productions (id),
    foreign key (id_platformy) references platforms (id)
);