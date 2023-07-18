select * from recipes where item_id = 497;
select id from recipes where item_id = 497;

insert into require_items (recipe_id, item_id, item_count) VALUES ((select id from recipes where item_id = 497), 53, 42);

set @itemID = 497;

select * from recipes where item_id = 497;
select * from recipes where item_id = @item_id;

# set @itemID = 497;
# set @itemID = 497123;
set @itemID = (select id from recipes where item_id = 497);
select @itemID;
select * from recipes where item_id = @itemID;

set @table_name = 'recipes';
set @col = 'id';
select @col;
# set @id = 497;
select * from @table_name;
select * from recipes;
select @col, id from recipes;

set @id = 2;
select * from recipes where id = @id;

SET foreign_key_checks = 0;
truncate table require_items;
truncate table recipes;
truncate table items;

SET foreign_key_checks = 0;
drop table require_items;
drop table recipes;
drop table items;

WITH RECURSIVE main_query AS (select * from require_items where recipe_id = (select r1.id from recipes as r1 where item_id = 497)
UNION ALL
select * from require_items ri where recipe_id = (select r2.id from recipes as r2, main_query as mq where r2.item_id = mq.item_id)
# select * from require_items ri
)
SELECT * FROM main_query;

# 497

WITH RECURSIVE main_query_02 AS (
select
    r1.id as r_id,
    r1.item_id as r_item_id,
    ri1.id as ri_id,
    ri1.item_id as ri_item_id,
    ri1.item_count
from require_items ri1
    left join recipes r1 on r1.id = ri1.recipe_id
where r1.item_id = 497
UNION ALL
    select
        r2.id,
        r2.item_id,
        ri2.id,
        ri2.item_id,
        ri2.item_count
    from recipes r2
        left join require_items ri2 on r2.id = ri2.recipe_id
        left join items i1 on i1.id = r2.item_id,
        main_query_02 mq02
    where
        r2.item_id = mq02.ri_item_id
)

SELECT
    ri_item_id, sum(item_count) as s
FROM main_query_02
    left join items i on i.id = ri_item_id
where
    i.category = 'resource'
group by ri_item_id
order by s desc
;

select r.id from recipes as r where item_id = 497;

select * from require_items where recipe_id = (select r.id from recipes as r where item_id = 497);
select ri.item_id, ri.item_count from require_items ri left join recipes r on r.id = ri.recipe_id where r.item_id = 497;
select ri.*, ri.* from require_items ri left join recipes r on r.id = ri.recipe_id where r.item_id = 497;

select recipe_id, sum(item_count) from require_items group by recipe_id;