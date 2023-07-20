<?php

namespace App\Commands\SandboxCommands;

use App\Services\DataManager;
use App\Services\PriceController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PriceSandboxCommand extends Command
{
    protected static $defaultName = 'sandbox.prices';
    private PriceController $_priceController;
    private \PDO $_pdo;
    private DataManager $_dataManager;

    public function __construct(PriceController $priceController, \PDO $pdo, DataManager $dataManager)
    {
        $this->_priceController = $priceController;
        $this->_pdo = $pdo;
        $this->_dataManager = $dataManager;
        parent::__construct(static::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_main();
//        $this->_resourcesPricesQuery();

        return 0;
    }

    private function _devExpand(): void
    {
        $query =
            /** @lang MySQL */
            'WITH RECURSIVE main_query_02 AS (
            select
                r1.id as r_id,
                r1.item_id as r_item_id,
                ri1.id as ri_id,
                ri1.item_id as ri_item_id,
                ri1.item_count
            from require_items ri1
                left join recipes r1 on r1.id = ri1.recipe_id
            where r1.item_id = :itemID
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
                ri_item_id,
                sum(item_count) as total,
                i.name as item_name
            FROM main_query_02
                left join items i on i.id = ri_item_id
            where
                i.category = \'resource\'
            group by ri_item_id
            order by total desc'
        ;

        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':itemID', 497);
        $stmt->execute();

        $result = $stmt->fetchAll();
//        dd($result);

        $lineSeparator = str_repeat('-', 32) . PHP_EOL;
        echo $lineSeparator;
        echo '| Icarus VII' . PHP_EOL;
        echo $lineSeparator;
        foreach ($result as $item) {
            echo vsprintf('| %s | %s | %s |' . PHP_EOL, [
                $item['ri_item_id'],
                $item['item_name'],
                $item['total'],
            ]);
        }
    }

    private function _main()
    {
        $ID = 497;
//        $ID = 186;
//        $ID = 163;
        $this->_priceController->optimalRoute($ID);

        $IDs = [
            497,
            482,
            483,
            112,
            379,
            389,
            383,
            395,
            108,
            //basic:
            163,
            126,
            176,
            186,
            109,
            172,
        ];
//        foreach ($IDs as $ID) {
//            $this->_priceController->optimalRoute($ID);
//        }
    }

    private function _resourcesPricesQuery()
    {
//        $ID = 497;
//
//        $query =
//            'select
//                r.item_id,
//                ri.item_id,
//                ri.item_count
//            from require_items ri
//                left join recipes r on r.id = ri.recipe_id
//                left join items i on r.item_id = i.id'
//        ;
//        $stmt = $this->_pdo->prepare($query);
////        $stmt->bindValue(':item_id', $ID);
//        $stmt->execute();
//
//        $result = $stmt->fetchAll();
//        dump($result);

        dump($this->_dataManager->findAllRequireItems());
    }
}