drop table if exists production_category;
create table production_category
(
    id_produkcji integer not null,
    id_kategorii integer not null,
    primary key (id_produkcji, id_kategorii),
    foreign key (id_produkcji) references productions (id),
    foreign key (id_kategorii) references categories (id)
);