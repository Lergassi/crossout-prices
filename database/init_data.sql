# start transaction;

set @scrap_meta_id = 53;
set @wires_id = 85;
set @copper_id = 43;
set @plastic_id = 785;
set @electronics_id = 201;

insert into items (id, name, category, craftable, quality, faction) VALUES (497, 'Icarus VII', 'weapons', 1, 'epic', 'dawn_children');

# Пока без стаков.
insert into items (id, name, category, craftable, quality, faction) VALUES (53, 'Scrap Metal x100', 'resource', 1, 'common', null);
insert into items (id, name, category, craftable, quality, faction) VALUES (85, 'Wires x100', 'resource', 1, 'common', null);
insert into items (id, name, category, craftable, quality, faction) VALUES (43, 'Copper x100', 'resource', 1, 'common', null);
insert into items (id, name, category, craftable, quality, faction) VALUES (785, 'Plastic x100', 'resource', 1, 'common', null);

insert into items (id, name, category, craftable, quality, faction) VALUES (482, 'Lunar IV ST', 'movement', 1, 'special', 'dawn_children');
insert into items (id, name, category, craftable, quality, faction) VALUES (483, 'Lunar IV', 'movement', 1, 'special', 'dawn_children');
insert into items (id, name, category, craftable, quality, faction) VALUES (112, 'Dun horse', 'movement', 1, 'special', 'nomads');

insert into items (id, name, category, craftable, quality, faction) VALUES (379, 'Racing wheel ST', 'movement', 1, 'rare', 'nomads');
insert into items (id, name, category, craftable, quality, faction) VALUES (389, 'Landing gear ST', 'movement', 1, 'rare', 'nomads');
insert into items (id, name, category, craftable, quality, faction) VALUES (163, 'Medium wheel ST', 'movement', 1, 'common', 'engineers');
insert into items (id, name, category, craftable, quality, faction) VALUES (126, 'Small wheel ST', 'movement', 1, 'common', 'engineers');

insert into items (id, name, category, craftable, quality, faction) VALUES (383, 'Racing wheel', 'movement', 1, 'rare', 'nomads');
insert into items (id, name, category, craftable, quality, faction) VALUES (395, 'Landing gear', 'movement', 1, 'rare', 'nomads');
insert into items (id, name, category, craftable, quality, faction) VALUES (186, 'Medium wheel', 'movement', 1, 'common', 'engineers');
insert into items (id, name, category, craftable, quality, faction) VALUES (176, 'Small wheel', 'movement', 1, 'common', 'engineers');

insert into items (id, name, category, craftable, quality, faction) VALUES (108, 'R-2 Chill', 'hardware', 1, 'rare', 'engineers');
insert into items (id, name, category, craftable, quality, faction) VALUES (109, 'R-1 Breeze', 'hardware', 1, 'common', 'engineers');
insert into items (id, name, category, craftable, quality, faction) VALUES (172, 'Radio', 'hardware', 'common', 1, 'engineers');

insert into recipes (craft_cost, result_count, item_id) values (15, 1, 497);

insert into recipes (craft_cost, result_count, item_id) values (6, 2, 482);
insert into recipes (craft_cost, result_count, item_id) values (6, 2, 483);
insert into recipes (craft_cost, result_count, item_id) values (6, 1, 112);

insert into recipes (craft_cost, result_count, item_id) values (3, 2, 379);
insert into recipes (craft_cost, result_count, item_id) values (3, 2, 389);

insert into recipes (craft_cost, result_count, item_id) values (0, 1, 163);
insert into recipes (craft_cost, result_count, item_id) values (0, 1, 126);

insert into recipes (craft_cost, result_count, item_id) values (3, 2, 383);
insert into recipes (craft_cost, result_count, item_id) values (3, 2, 395);

insert into recipes (craft_cost, result_count, item_id) values (0, 1, 186);
insert into recipes (craft_cost, result_count, item_id) values (0, 1, 176);

insert into recipes (craft_cost, result_count, item_id) values (3, 1, 108);

insert into recipes (craft_cost, result_count, item_id) values (0, 1, 109);
insert into recipes (craft_cost, result_count, item_id) values (0, 1, 172);

set @item_id = (select id from recipes where item_id = 497);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @scrap_meta_id, 100);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @wires_id, 200);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @copper_id, 150);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @plastic_id, 100);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, 482, 2);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, 483, 2);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, 112, 2);

set @item_id = (select id from recipes where item_id = 482);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @scrap_meta_id, 50);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @wires_id, 100);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @copper_id, 100);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @plastic_id, 50);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, 379, 2);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, 389, 2);

set @item_id = (select id from recipes where item_id = 379);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @scrap_meta_id, 600);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @copper_id, 130);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, 163, 1);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, 126, 1);

set @item_id = (select id from recipes where item_id = 163);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @scrap_meta_id, 15);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @copper_id, 3);

set @item_id = (select id from recipes where item_id = 126);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @scrap_meta_id, 15);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @copper_id, 3);

set @item_id = (select id from recipes where item_id = 389);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @scrap_meta_id, 600);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @copper_id, 130);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, 126, 1);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, 163, 1);

set @item_id = (select id from recipes where item_id = 483);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @scrap_meta_id, 50);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @wires_id, 100);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @copper_id, 100);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @plastic_id, 50);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, 383, 2);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, 395, 2);

set @item_id = (select id from recipes where item_id = 383);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @scrap_meta_id, 600);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @copper_id, 130);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, 186, 1);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, 176, 1);

set @item_id = (select id from recipes where item_id = 186);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @scrap_meta_id, 15);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @copper_id, 3);

set @item_id = (select id from recipes where item_id = 176);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @scrap_meta_id, 15);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @copper_id, 3);

set @item_id = (select id from recipes where item_id = 395);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @scrap_meta_id, 600);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @copper_id, 130);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, 186, 1);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, 176, 1);

set @item_id = (select id from recipes where item_id = 112);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @scrap_meta_id, 50);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @wires_id, 100);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @copper_id, 100);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @plastic_id, 50);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, 383, 2);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, 108, 1);

set @item_id = (select id from recipes where item_id = 108);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @scrap_meta_id, 650);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @copper_id, 130);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, 109, 1);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, 172, 1);

set @item_id = (select id from recipes where item_id = 109);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @scrap_meta_id, 20);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @copper_id, 4);

set @item_id = (select id from recipes where item_id = 172);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @scrap_meta_id, 20);
insert into require_items (recipe_id, item_id, item_count) VALUES (@item_id, @copper_id, 4);

insert into prices (max_sell_price, min_buy_price, item_id) values (4.13, 4.13, @scrap_meta_id);
insert into prices (max_sell_price, min_buy_price, item_id) values (12.20, 11.83, @wires_id);
insert into prices (max_sell_price, min_buy_price, item_id) values (3.70, 3.39, @copper_id);
insert into prices (max_sell_price, min_buy_price, item_id) values (33.97, 32.67, @plastic_id);

insert into prices (max_sell_price, min_buy_price, item_id) values (459.90, 445.08, 497);

insert into prices (max_sell_price, min_buy_price, item_id) values (62.78, 57.23, 482);
insert into prices (max_sell_price, min_buy_price, item_id) values (63.88, 61.41, 483);
insert into prices (max_sell_price, min_buy_price, item_id) values (129.62, 110.54, 112);

insert into prices (max_sell_price, min_buy_price, item_id) values (20.00, 16.00, 379);
insert into prices (max_sell_price, min_buy_price, item_id) values (19.76, 16.74, 389);

insert into prices (max_sell_price, min_buy_price, item_id) values (0.94, 0.55, 163);
insert into prices (max_sell_price, min_buy_price, item_id) values (0.90, 0.42, 126);

insert into prices (max_sell_price, min_buy_price, item_id) values (19.00, 16.73, 383);
insert into prices (max_sell_price, min_buy_price, item_id) values (20.24, 16.91, 395);

insert into prices (max_sell_price, min_buy_price, item_id) values (0.90, 0.67, 186);
insert into prices (max_sell_price, min_buy_price, item_id) values (1.00, 0.50, 176);

insert into prices (max_sell_price, min_buy_price, item_id) values (43.93, 34.51, 108);

insert into prices (max_sell_price, min_buy_price, item_id) values (1.20, 0.74, 109);
insert into prices (max_sell_price, min_buy_price, item_id) values (1.22, 0.74, 172);

# commit;

# rollback;