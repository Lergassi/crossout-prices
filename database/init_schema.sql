create table if not exists items
(
    id int unsigned,    # Как в crossoutdb.
    name varchar(128),
    category varchar(32) not null,
    quality varchar(32) not null,
    faction varchar(32) null,
    primary key (id)
);

create table if not exists recipes
(
    id int unsigned auto_increment,
    craft_cost int unsigned not null,
#     craft_time int unsigned not null,
    result_count int unsigned not null,
    item_id int unsigned not null,
    primary key (id),
    foreign key (item_id) references items (id),
    unique (item_id)
);

create table if not exists require_items
(
    id int unsigned auto_increment,
    recipe_id int unsigned not null,
    item_id int unsigned not null,
    item_count int unsigned not null,
    primary key (id),
    foreign key (recipe_id) references recipes (id),
    foreign key (item_id) references items (id),
    unique (recipe_id, item_id)
);

# prices