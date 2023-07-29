create table if not exists items
(
    id int unsigned,
    name varchar(128),
    category varchar(32) not null,
    craftable bool not null,
    quality varchar(32) not null,
    faction varchar(32) null,
    available_craft bool not null,
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

# Цена продажи: максимальная с ожиданием, минимальная с мгновенной продажей.
# Цена покупки: максимальная с мгновенной покупкой, минимальная с ожиданием.
# Противоположные цены не будут учитываться.
create table prices
(
    id int unsigned auto_increment,
    max_sell_price decimal(19, 2) unsigned not null,
    min_buy_price decimal(19, 2) unsigned not null,
    item_id int unsigned not null,
    c_optimal_craft_cost float unsigned null,
    c_profit float null,
    c_type varchar(16) null,
    c_optimal_craft_cost_date timestamp null,
    primary key (id),
    foreign key (item_id) references items (id),
    unique (item_id)
);