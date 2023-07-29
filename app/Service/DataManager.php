<?php

namespace App\Service;

use App\Types\CategoryID;

class DataManager
{
    private \PDO $_pdo;

    /**
     * @param \PDO $_pdo
     */
    public function __construct(\PDO $_pdo)
    {
        $this->_pdo = $_pdo;
    }

    public function addItem(
        int $ID,
        string $name,
        string $category,
        string $quality,
        string|null $faction = null,
    ): void
    {
        $query = 'insert into items (id, name, category, quality, faction) values (:id, :name, :category, :quality, :faction)';
        $stmt = $this->_pdo->prepare($query);

        $stmt->bindValue(':id', $ID);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':category', $category);
        $stmt->bindValue(':quality', $quality);
        $stmt->bindValue(':faction', $faction);

        $stmt->execute();
    }

    /**
     * Без учёта Resources: repair kit и тд.
     * @return array
     */
    public function findCraftableItems(): array
    {
        $query = 'select * from items where category <> :category and craftable = :craftable';
        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':category', CategoryID::Resources->value);
        $stmt->bindValue(':craftable', 1);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findOneItem(int $ID): array
    {
        $query = 'select * from items where id = :id';
        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':id', $ID);

        $stmt->execute();

        return $stmt->fetch();
    }

    public function findItems(): array
    {
        $query = 'select * from items';
        $stmt = $this->_pdo->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function hasItem(int $ID): bool
    {
        $query = 'select count(*) as count from items where id = :id';
        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':id', $ID);

        $stmt->execute();

        return $stmt->fetch()['count'] === 1;
    }

    public function findOnePrice(int $itemID): array
    {
        $query = 'select * from prices where item_id = :item_id';
        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':item_id', $itemID);

        $stmt->execute();

        return $stmt->fetch();
    }

    public function findPrices(): array
    {
        $query = 'select * from prices order by c_profit desc';
        $stmt = $this->_pdo->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function totalItemProfits(bool $onlyAvailableCraft = false): array
    {
        $query = 'select p.*, i.name as i_name, i.category as i_category from prices p left join items i on i.id = p.item_id where i.category <> :category and i.craftable = :craftable';
        if ($onlyAvailableCraft) {
            $query .= ' and i.available_craft = :available_craft';
        }
        $query .= ' order by p.c_profit';

        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':category', CategoryID::Resources->value);
        $stmt->bindValue(':craftable', 1);
        if ($onlyAvailableCraft) $stmt->bindValue(':available_craft', 1);

        $stmt->execute();

        return $stmt->fetchAll();
    }
    
    public function findOneRecipe(int $itemID): array
    {
        $query = 'select * from recipes where item_id = :item_id';
        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':item_id', $itemID);

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * @deprecated
     * @param int $itemID
     * @return array
     */
    public function _findRequireItemsWithJoin(int $itemID): array
    {
        $query = 'select * from require_items left join recipes r on r.id = require_items.recipe_id where r.item_id = :item_id';
        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':item_id', $itemID);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findRequireItemsWithJoin(int $itemID): array
    {
        $query = 'select ri.*, i.name as i_name from require_items ri left join recipes r on r.id = ri.recipe_id left join items i on ri.item_id = i.id where r.item_id = :item_id';
        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':item_id', $itemID);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findRequireItemsWithoutResources(int $itemID): array
    {
        $query = 'select i.name as i_name, ri.item_id as ri_item_id, ri.item_count as ri_item_count from require_items ri left join recipes r on r.id = ri.recipe_id left join items i on ri.item_id = i.id where r.item_id = :item_id and i.category <> :category';
        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':item_id', $itemID);
        $stmt->bindValue(':category', CategoryID::Resources->value);

        $stmt->execute();

        return $stmt->fetchAll();
    }

//    public function findRequireItems(int $itemID): array
//    {
//        $query = 'select ri.* from require_items ri left join recipes r on r.id = ri.recipe_id where r.item_id = :item_id';
//        $stmt = $this->_pdo->prepare($query);
//        $stmt->bindValue(':item_id', $itemID);
//
//        $stmt->execute();
//
//        return $stmt->fetchAll();
//    }

    //todo: Заготовка для стоимости ресурсов в 1 запрос.
    public function findAllRequireItems(): array
    {
        $query =
            'select
                r.item_id as r_item_id,
                i.category as i_category,
                ri.item_id as ri_item_id,
                ri.item_count as ri_item_count
            from require_items ri
                left join recipes r on r.id = ri.recipe_id
                left join items i on ri.item_id = i.id
            where i.category = :category'
        ;
        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':category', CategoryID::Resources->value);
        $stmt->execute();

        return $stmt->fetchAll();
    }

//    public function findRecipes(): array {return [];}

    public function findHierarchyRequireItems(int $ID): array
    {
        // v0.0.2
        $query =
            /** @lang MySQL */
            'WITH RECURSIVE query AS (
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
                r1.item_id = :item_id

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
            left join items i on i.id = ri_item_id'
        ;

        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':item_id', $ID);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findHierarchyPrices(int $ID): array
    {
        // v0.0.2
        $query =
            /** @lang MySQL */
            'WITH RECURSIVE query AS (
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
        r1.item_id = :item_id

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
    i.name as i_name,
    i.category as i_category,
    i_quality,
    p.min_buy_price as p_min_buy_price,
    p.c_optimal_craft_cost as p_optimal_craft_cost,
    p.c_profit as p_profit,
    p.c_type as p_type,
    r.craft_cost as r_craft_cost,
    level
FROM query q
    left join items i on i.id = ri_item_id
    left join prices p on i.id = p.item_id
    left join recipes r on r.item_id = i.id'
        ;

        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':item_id', $ID);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function updateOptimalCraft(int $ID, float $optimalCraftCost, float $profit, string $type, \DateTime $date = null): void
    {
        if (!$date) $date = new \DateTime();

        $updateQuery = 'update prices set c_optimal_craft_cost = :c_optimal_craft_cost, c_profit = :c_profit, c_type = :c_type, c_optimal_craft_cost_date = :c_optimal_craft_cost_date where item_id = :item_id';
        $updateStmt = $this->_pdo->prepare($updateQuery);
        $updateStmt->bindValue(':c_optimal_craft_cost', $optimalCraftCost);
        $updateStmt->bindValue(':c_profit', $profit);
        $updateStmt->bindValue(':c_type', $type);
        $updateStmt->bindValue(':c_optimal_craft_cost_date', $date->format('Y-m-d H:i:s'));
        $updateStmt->bindValue(':item_id', $ID);
        $updateStmt->execute();
    }
}