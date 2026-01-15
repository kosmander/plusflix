create table production_tag
(
    id_produkcji integer not null,
    id_tagu integer not null,
    primary key (id_produkcji, id_tagu),
    foreign key (id_produkcji) references productions (id),
    foreign key (id_tagu) references tags (id)
);