<?php

namespace App\Services;

use App\Types\CategoryID;

class PriceController
{
    private \PDO $_pdo;
    private DataManager $_dataManager;

    public function __construct(\PDO $pdo, DataManager $dataManager)
    {
        $this->_pdo = $pdo;
        $this->_dataManager = $dataManager;
    }

    public function optimalRoute(int $ID): void
    {
        $originalItemID = $ID;

        $result = $this->_dataManager->findHierarchyRequireItems($ID);
        if (!count($result)) throw new \Exception(sprintf('Данные для %s не найдены.', $ID));

        $stacks = [
            43 => 100,  //copper
            53 => 100,
            85 => 100,
            785 => 100,
        ];

        $resourcePrices = [];
        $resultCount = count($result);
        $stack = [];
        $reversStack = [$ID];
        for ($i = 0; $i < $resultCount; ++$i) {
            if ($result[$i]['r_item_id'] === $ID) {
                $recipe = $this->_dataManager->findOneRecipe($result[$i]['r_item_id']);
                $price = $this->_dataManager->findOnePrice($result[$i]['ri_item_id']);
                if (!isset($resourcePrices[$result[$i]['r_item_id']])) $resourcePrices[$result[$i]['r_item_id']] = $recipe['craft_cost'];

                if ($result[$i]['i_category'] === CategoryID::Resource->value) {
                    $resourcePrices[$result[$i]['r_item_id']] += round($result[$i]['ri_item_count'] / $stacks[$result[$i]['ri_item_id']] * $price['min_buy_price'], 2);
                } else {
                    $stack[] = $result[$i]['ri_item_id'];
                    $reversStack[] = $result[$i]['ri_item_id'];
                }
            }//end if

            if ($i + 1 >= $resultCount) {
                if (count($stack)) {
                    $ID = array_shift($stack);
                    $i = -1;
                }
            }
        }//end for
//        dump($resourcePrices);

        $total = [];
        $reversStackCount = count($reversStack);
        for ($i = $reversStackCount - 1; $i >= 0; --$i) {
            //todo: Оптимизировать и убрать повторные запросы.
            $price = $this->_dataManager->findOnePrice($reversStack[$i]);
            $recipe = $this->_dataManager->findOneRecipe($result[$i]['r_item_id']);
            $requireItems = [];
            for ($j = 0; $j < $resultCount; ++$j) {
                if ($result[$j]['r_item_id'] === $reversStack[$i] && $result[$j]['i_category'] !== CategoryID::Resource->value) {
                    $requireItems[] = $result[$j];
                }
            }

            $total[$reversStack[$i]] = [
                'craft' => $resourcePrices[$reversStack[$i]],
                'buy' => floatval($price['min_buy_price']), //todo: Убрать/скрыть в отдельный класс работу с decimal/float + round.
            ];

            if (count($requireItems)) {
                foreach ($requireItems as $requireItem) {
                    $buySum = round($total[$requireItem['ri_item_id']]['buy'] * $requireItem['ri_item_count'], 2);
                    if ($total[$requireItem['ri_item_id']]['craft'] >= $buySum) {
//                        $total[$reversStack[$i]]['craft'] += $buySum;
                        $total[$reversStack[$i]]['craft'] = round($total[$reversStack[$i]]['craft'] + $buySum, 2);
                    } else {
                        $total[$reversStack[$i]]['craft'] = round($total[$reversStack[$i]]['craft'] + $total[$requireItem['ri_item_id']]['craft'], 2);
                    }
                }
            }
        }
//        dump($total);

        //todo: Сделать таблицу или найти библиотеку. Нужно выровнить табы.
        $separator = str_repeat('-', 64) . PHP_EOL;
        echo $separator;
        echo sprintf("| Item: %s", $originalItemID) . PHP_EOL;
        echo $separator;
        echo sprintf("| ID\t| craft\t| buy" . PHP_EOL);
        echo $separator;
        foreach ($total as $key => $item) {
            echo vsprintf("| %s\t| %s\t| %s" . PHP_EOL, [
                $key,
                $item['craft'],
                $item['buy'],
            ]);
        }
        echo $separator;
    }
}