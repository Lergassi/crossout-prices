alter table prices
    add c_optimal_craft_cost float unsigned null,
    add c_profit float null,
    add c_type varchar(16) null,
    add c_optimal_craft_cost_date timestamp null
;

alter table items
    add craftable bool not null
;

alter table items
    add available_craft bool not null
;

alter table items
    add is_feature bool not null
;
