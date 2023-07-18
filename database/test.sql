# rollback;
start transaction;
set autocommit = 1;
insert into items (id, name, category, quality, faction) VALUES (497, 'Icarus VII', 'weapons', 'epic', 'dawn_children');
insert into items (id, name, category, quality, faction) VALUES (497, 'Icarus VII', 'weapons', 'epic', 'dawn_children');
insert into items (id, name, category, quality, faction) VALUES (42, 'Icarus VII', 'weapons', 'epic', 'dawn_children');
commit;

# rollback;

# select @autocommit;