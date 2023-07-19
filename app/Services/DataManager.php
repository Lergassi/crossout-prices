<?php

namespace App\Services;

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

    public function findRecipe(): array {return [];}

    public function findHierarchyRequireItems(int $ID): array
    {
        // v0.0.1
        $query =
            /** @lang MySQL */
            'WITH RECURSIVE query AS (
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
                r1.item_id = :item_id
        
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
                left join items i on i.id = ri_item_id'
        ;

        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':item_id', $ID);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}