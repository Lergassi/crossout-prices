create table if not exists test_table
(
    id int unsigned auto_increment,
    text text null,
    decimal_value decimal(19, 2) null,
    float_value float null,
    primary key (id)
);

insert into test_table (text, decimal_value, float_value) values ('text01', 1.42, 1.5555555);
