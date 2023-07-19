select * from items;
select
    i.*,
    p.max_sell_price,
    p.min_buy_price
from items i
left join prices p on p.item_id = i.id
# right join prices p on i.id = p.item_id
where p.id is not null
order by id
;

SET foreign_key_checks = 0;
truncate table require_items;
truncate table recipes;
truncate table prices;
truncate table items;
SET foreign_key_checks = 1;

SET foreign_key_checks = 0;
drop table require_items;
drop table recipes;
drop table prices;
drop table items;
SET foreign_key_checks = 1;

# Expand стратегия для расчетов всех ресурсов.
# v0.0.1
set @item_id = 497;
WITH RECURSIVE main_query_02 AS (
    select
        r1.id as r_id,
        r1.item_id as r_item_id,
        ri1.id as ri_id,
        ri1.item_id as ri_item_id,
        ri1.item_count
    from require_items ri1
             left join recipes r1 on r1.id = ri1.recipe_id
    where r1.item_id = @item_id
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

# Иерархия всех предметов и ресурсов.
# v0.0.1
set @item_id = 497;
WITH RECURSIVE query AS (
    select
        r1.id as r_id,
        r1.item_id as r_item_id,
        ri1.id as ri_id,
        ri1.item_id as ri_item_id,
        ri1.item_count as ri_item_count,
        0 as level
    from require_items ri1
        left join recipes r1 on r1.id = ri1.recipe_id
    where
        r1.item_id = @item_id

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
        query q
    where
        r2.item_id = q.ri_item_id
)

SELECT
    r_item_id,
    ri_item_id,
    ri_item_count,
    i.category as i_category,
    level
FROM query
    left join items i on i.id = ri_item_id
;
