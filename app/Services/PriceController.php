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

    public function optimalRoute(int $ID): void
    {
        $originalItemID = $ID;

        $hierarchyResult = $this->_dataManager->findHierarchyRequireItems($ID);
        if (!count($hierarchyResult)) throw new \Exception(sprintf('Данные для ID=%s не найдены.', $ID));

        //todo: Убрать в бд.
        $itemStacks = [
            43 => 100,  //copper
            53 => 100,  //scrap
            85 => 100,
            785 => 100,
            168 => 100,
            337 => 100,
            784 => 100,
            919 => 100,
        ];

        $resultCount = count($hierarchyResult);
        $parentItemID = 0;
        $resourcePrices = [];
        $itemResourcePrices = [];
        $queue = [];
        $reversQueue = [$ID];
        for ($i = 0; $i < $resultCount; ++$i) {
            if ($hierarchyResult[$i]['r_item_id'] === $ID && $hierarchyResult[$i]['r_parent_item_id'] === $parentItemID) {
                if ($hierarchyResult[$i]['i_category'] === CategoryID::Resources->value) {
                    $price = $this->_dataManager->findOnePrice($hierarchyResult[$i]['ri_item_id']);
                        $itemResourcePrices[$hierarchyResult[$i]['r_item_id']][$hierarchyResult[$i]['ri_item_id']] = round($hierarchyResult[$i]['ri_item_count'] / $itemStacks[$hierarchyResult[$i]['ri_item_id']] * $price['min_buy_price'], 2);
//                    }
                } else {
                    $queue[] = [
                        'r_item_id' => $hierarchyResult[$i]['r_item_id'],
                        'ri_item_id' => $hierarchyResult[$i]['ri_item_id'],
                    ];
                    $reversQueue[] = $hierarchyResult[$i]['ri_item_id'];
                }
            }//end if

            if ($i + 1 >= $resultCount) {
                if (!isset($resourcePrices[array_key_first($itemResourcePrices)])) {
                    $sum = 0;
                    foreach ($itemResourcePrices as $key => $itemResourcePrice) {
                        if (!isset($resourcePrices[$key])) {
                            $requireItemRecipe = $this->_dataManager->findOneRecipe($key);
                            $resourcePrices[$key] = [
                                'value' => $requireItemRecipe['craft_cost'],
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

        $total = [];
        $reversQueueCount = count($reversQueue);
        for ($i = $reversQueueCount - 1; $i >= 0; --$i) {
            $price = $this->_dataManager->findOnePrice($reversQueue[$i]);
            $recipe = $this->_dataManager->findOneRecipe($reversQueue[$i]); //todo: join с предметами.
            $requireItems = $this->_dataManager->findRequireItemsWithoutResources($reversQueue[$i]);

            $total[$reversQueue[$i]] = [
                'craft' => $resourcePrices[$reversQueue[$i]]['value'],
                'buy' => floatval($price['min_buy_price']), //todo: Убрать/скрыть в отдельный класс работу с decimal/float + round.
                'type' => 'craft',  //По умолчанию пока крафт, чтобы реализовать ресурсы. Позже стратегия может измениться. todo: Вообще значение должно быть null для удобного дебага (и все остальные), но для единой логики пусть будет 'craft'.
            ];

            if (count($requireItems)) {
                foreach ($requireItems as $requireItem) {
                    $requireItemRecipe = $this->_dataManager->findOneRecipe($requireItem['ri_item_id']);
                    $buySum = round($total[$requireItem['ri_item_id']]['buy'] * $requireItem['ri_item_count'], 2);
                    $craftSum = round($total[$requireItem['ri_item_id']]['craft'] * $requireItem['ri_item_count'] / $requireItemRecipe['result_count'], 2);
                    if ($craftSum >= $buySum) {
                        $total[$reversQueue[$i]]['craft'] = round($total[$reversQueue[$i]]['craft'] + $buySum, 2);
                    } else {
                        $total[$reversQueue[$i]]['craft'] = round($total[$reversQueue[$i]]['craft'] + $craftSum, 2);
                    }
                }//end foreach
            }//end if count(requireItems)

            if ($total[$reversQueue[$i]]['craft'] >= $total[$reversQueue[$i]]['buy'] * $recipe['result_count']) {
                $total[$reversQueue[$i]]['type'] = 'buy';
            }
        }

        //todo: Сделать таблицу или найти библиотеку. Нужно выровнить табы.
        $separator = str_repeat('-', 64) . PHP_EOL;
        echo $separator;
        echo sprintf("| Item: %s", $originalItemID) . PHP_EOL;
        echo $separator;
        echo sprintf("| ID\t| craft\t| buy | type " . PHP_EOL);
        echo $separator;
        foreach ($total as $key => $item) {
            echo vsprintf("| %s\t| %s\t| %s | %s" . PHP_EOL, [
                $key,
                $item['craft'],
                $item['buy'],
                $item['type'],
            ]);
        }
        echo $separator;
//        echo sprintf('Profit: %s.' . PHP_EOL, round($total[array_key_last($total)]['buy'] - $total[array_key_last($total)]['craft'], 2));
        echo $separator;
    }
}