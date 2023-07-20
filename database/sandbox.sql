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

# tree
WITH RECURSIVE main_query_02 AS (
    select
        r1.id as r_id,
        r1.item_id as r_item_id,
        ri1.id as ri_id,
        ri1.item_id as ri_item_id,
        ri1.item_count,
        0 as level
    from require_items ri1
             left join recipes r1 on r1.id = ri1.recipe_id
    where r1.item_id = 497

    UNION ALL

    select
        r2.id,
        r2.item_id,
        ri2.id,
        ri2.item_id,
        ri2.item_count,
        level + 1
    from recipes r2
             left join require_items ri2 on r2.id = ri2.recipe_id
             left join items i1 on i1.id = r2.item_id,
         main_query_02 mq02
    where
            r2.item_id = mq02.ri_item_id
)

SELECT
    r_item_id,
    ri_item_id,
    item_count,
    i.category,
    level
FROM main_query_02
         left join items i on i.id = ri_item_id
;

select * from test_table;


# v0.0.2
set @item_id = 497;
WITH RECURSIVE query AS (
    select
        r1.id as r_id,
        r1.item_id as r_item_id,
        ri1.id as ri_id,
        ri1.item_id as ri_item_id,
        ri1.item_count as ri_item_count,
        i1.quality as i_quality,
        0 as level,
        0 as r_parent_item_id
    from recipes r1
        left join require_items ri1 on r1.id = ri1.recipe_id
        left join items i1 on i1.id = r1.item_id
    where
        r1.item_id = @item_id

    UNION ALL

    select
        ri2.recipe_id,
        r2.item_id,
        ri2.id,
        ri2.item_id,
        ri2.item_count,
        i2.quality,
        level + 1,
        q.r_item_id
    from recipes r2
             left join require_items ri2 on r2.id = ri2.recipe_id
             left join items i2 on i2.id = r2.item_id,
        query q
    where
        r2.item_id = q.ri_item_id
)

SELECT
    r_id,
    r_parent_item_id,
    r_item_id,
    ri_item_id,
    ri_item_count,
    i.category as i_category,
    i_quality,
    level
FROM query q
    left join items i on i.id = ri_item_id
;

select
#     *
r.id,
r.item_id,
ri.recipe_id,
ri.item_id,
ri.item_count
from recipes r
         left join require_items ri on r.id = ri.recipe_id
# where ri.recipe_id = 3 or ri.recipe_id = 4
where ri.item_id = 383
;


select
    r.item_id,
    i.category,
    ri.item_id,
    ri.item_count
from require_items ri
    left join recipes r on r.id = ri.recipe_id
    left join items i on ri.item_id = i.id
where i.category = 'resource'