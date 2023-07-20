<?php

namespace App\Services;

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

    public function findOnePrice(int $itemID): array
    {
        $query = 'select * from prices where item_id = :item_id';
        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':item_id', $itemID);

        $stmt->execute();

        return $stmt->fetch();
    }
    
    public function findPrice(): array {return [];}
    public function findOneRecipe(int $itemID): array
    {
        $query = 'select * from recipes where item_id = :item_id';
        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':item_id', $itemID);

        $stmt->execute();

        return $stmt->fetch();
    }

    public function findRequireItems(int $itemID): array
    {
        $query = 'select * from require_items left join recipes r on r.id = require_items.recipe_id where r.item_id = :item_id';
        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':item_id', $itemID);

        $stmt->execute();

        return $stmt->fetchAll();
    }

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
        $stmt->bindValue(':category', CategoryID::Resource->value);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findRecipe(): array {return [];}

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
}