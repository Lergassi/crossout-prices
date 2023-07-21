<?php

namespace App\Services;

use App\Types\CategoryID;

class PriceController
{
    private DataManager $_dataManager;

    public function __construct(DataManager $dataManager)
    {
        $this->_dataManager = $dataManager;
    }

    //todo: Оптимизировать: убрать повторные запросы.
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
            168 => 100,
            337 => 100,
            784 => 100,
            919 => 100,
        ];

        $resultCount = count($result);
        $parentItemID = 0;
        $resourcePrices = [];
        $itemResourcePrices = [];
        $queue = [];
        $reversQueue = [$ID];
        for ($i = 0; $i < $resultCount; ++$i) {
            if ($result[$i]['r_item_id'] === $ID && $result[$i]['r_parent_item_id'] === $parentItemID) {

//                if (!isset($resourcePrices[$result[$i]['r_item_id']])) {
//                    $resourcePrices[$result[$i]['r_item_id']] = [
//                        'value' => $recipe['craft_cost'],
//                    ];
//                }

                if ($result[$i]['i_category'] === CategoryID::Resources->value) {
                    $price = $this->_dataManager->findOnePrice($result[$i]['ri_item_id']);
//                    if (!isset($resourcePrices[$result[$i]['r_item_id']][$result[$i]['ri_item_id']])) {
//                        $resourcePrices[$result[$i]['r_item_id']][$result[$i]['ri_item_id']] = round($result[$i]['ri_item_count'] / $stacks[$result[$i]['ri_item_id']] * $price['min_buy_price'], 2);
//                    }
//                    if (!isset($itemResourcePrices[$result[$i]['r_item_id']][$result[$i]['ri_item_id']])) {
                        $itemResourcePrices[$result[$i]['r_item_id']][$result[$i]['ri_item_id']] = round($result[$i]['ri_item_count'] / $stacks[$result[$i]['ri_item_id']] * $price['min_buy_price'], 2);
//                    }
                } else {
                    $queue[] = [
                        'r_item_id' => $result[$i]['r_item_id'],
                        'ri_item_id' => $result[$i]['ri_item_id'],
                    ];
                    $reversQueue[] = $result[$i]['ri_item_id'];
                }
            }//end if

            if ($i + 1 >= $resultCount) {
                if (!isset($resourcePrices[array_key_first($itemResourcePrices)])) {
                    $sum = 0;
                    foreach ($itemResourcePrices as $key => $itemResourcePrice) {
                        if (!isset($resourcePrices[$key])) {
                            $recipe = $this->_dataManager->findOneRecipe($key);
                            $resourcePrices[$key] = [
                                'value' => $recipe['craft_cost'],
                            ];
                        }

                        foreach ($itemResourcePrice as $value) {
                            $sum += $value;
                        }
                        $resourcePrices[$key]['value'] += $sum;
                        $sum = 0;
                    }
                }
                $itemResourcePrices = [];

                if (count($queue)) {
                    $_item = array_shift($queue);
                    $ID = $_item['ri_item_id'];
                    $parentItemID = $_item['r_item_id'];
                    $i = -1;
                }
            }
        }//end for
//        dump($reversQueue);
//        dd($itemResourcePrices);
//        dd($resourcePrices);
//        dump($resourcePrices);

//        $resourcePrices = [
//            186 => ['value' => 0.72],
//            176 => ['value' => 0.72],
//            163 => ['value' => 0.72],
//            126 => ['value' => 0.72],
//            172 => ['value' => 0.97],
//            109 => ['value' => 0.97],
//            395 => ['value' => 32.19],
//            389 => ['value' => 32.19],
//            379 => ['value' => 32.19],
//            383 => ['value' => 30.68],
//            108 => ['value' => 34.26],
//            483 => ['value' => 39.63],
//            482 => ['value' => 39.63],
//            112 => ['value' => 39.63],
//            497 => ['value' => 80.55],
//        ];

        $total = [];
        $reversQueueCount = count($reversQueue);
        for ($i = $reversQueueCount - 1; $i >= 0; --$i) {
            $price = $this->_dataManager->findOnePrice($reversQueue[$i]);
            $recipe = $this->_dataManager->findOneRecipe($result[$i]['r_item_id']);
            $requireItems = [];
            for ($j = 0; $j < $resultCount; ++$j) {
                if ($result[$j]['r_item_id'] === $reversQueue[$i] && $result[$j]['i_category'] !== CategoryID::Resources->value) {
                    $requireItems[] = $result[$j];
                }
            }

            $total[$reversQueue[$i]] = [
                'craft' => $resourcePrices[$reversQueue[$i]]['value'],
                'buy' => floatval($price['min_buy_price']), //todo: Убрать/скрыть в отдельный класс работу с decimal/float + round.
            ];

            if (count($requireItems)) {
                foreach ($requireItems as $requireItem) {
                    $buySum = round($total[$requireItem['ri_item_id']]['buy'] * $requireItem['ri_item_count'], 2);
                    if ($total[$requireItem['ri_item_id']]['craft'] >= $buySum) {
                        $total[$reversQueue[$i]]['craft'] = round($total[$reversQueue[$i]]['craft'] + $buySum, 2);
                    } else {
                        $total[$reversQueue[$i]]['craft'] = round($total[$reversQueue[$i]]['craft'] + $total[$requireItem['ri_item_id']]['craft'], 2);
                    }
                }
            }
        }

        //todo: Сделать таблицу или найти библиотеку. Нужно выровнить табы.
        $separator = str_repeat('-', 64) . PHP_EOL;
        echo $separator;
        echo sprintf("| Item: %s", $originalItemID) . PHP_EOL;
        echo $separator;
        echo sprintf("| ID\t| craft\t| buy " . PHP_EOL);
        echo $separator;
        foreach ($total as $key => $item) {
            echo vsprintf("| %s\t| %s\t| %s " . PHP_EOL, [
                $key,
                $item['craft'],
                $item['buy'],
            ]);
        }
        echo $separator;
    }
}