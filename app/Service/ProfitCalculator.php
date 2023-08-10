<?php

namespace App\Service;

use App\Types\CategoryID;

class ProfitCalculator
{
    private float $_fee = 0.1;

    private DataManager $_dataManager;

    public function __construct(DataManager $dataManager)
    {
        $this->_dataManager = $dataManager;
    }

    public function calculateOptimalRoute(int $ID, \DateTime $date = null): void
    {
        $originalItemID = $ID;

        $hierarchyResult = $this->_dataManager->findHierarchyRequireItems($ID);
        if (!count($hierarchyResult)) throw new \Exception(sprintf('Данные для ID=%s не найдены.', $ID));

        //todo: Убрать в бд.
        $itemStacks = [
            43 => 100,  //copper
            53 => 100,  //metal
            85 => 100,
            785 => 100,
            168 => 100,
            337 => 100,
            784 => 100,
            919 => 100,
        ];

        $itemPrice = $this->_dataManager->findOnePrice($ID);
        $itemRecipe = $this->_dataManager->findOneRecipe($ID);
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
                } else {
                    if (!$hierarchyResult[$i]['available_craft']) continue;

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

            //todo: Получается нужно выбрать: рассчитывать с крафтом или без. И тут будет только одно решение. Другое решение нужно будет запускать отдельно.
            if (count($requireItems)) {
                foreach ($requireItems as $requireItem) {
                    if (!$requireItem['i_available_craft']) {
                        $_buySum = round($this->_dataManager->findOnePrice($requireItem['ri_item_id'])['min_buy_price'] * $requireItem['ri_item_count'], 2);    //todo: Метод.
                        $total[$reversQueue[$i]]['craft'] = round($total[$reversQueue[$i]]['craft'] + $_buySum, 2);
                    } else {
                        $requireItemRecipe = $this->_dataManager->findOneRecipe($requireItem['ri_item_id']);
                        $buySum = round($total[$requireItem['ri_item_id']]['buy'] * $requireItem['ri_item_count'], 2);
                        $craftSum = round($total[$requireItem['ri_item_id']]['craft'] * $requireItem['ri_item_count'] / $requireItemRecipe['result_count'], 2);
                        if ($craftSum >= $buySum) {
                            $total[$reversQueue[$i]]['craft'] = round($total[$reversQueue[$i]]['craft'] + $buySum, 2);
                        } else {
                            $total[$reversQueue[$i]]['craft'] = round($total[$reversQueue[$i]]['craft'] + $craftSum, 2);
                        }
                    }
                }//end foreach
            }//end if count(requireItems)

            if ($total[$reversQueue[$i]]['craft'] >= $total[$reversQueue[$i]]['buy'] * $recipe['result_count']) {
                $total[$reversQueue[$i]]['type'] = 'buy';
            }
        }

        $optimalCraftCost = $total[$originalItemID]['craft'];
        $type = $total[$originalItemID]['type'];
        $totalSellPrice = round($itemPrice['max_sell_price'] * $itemRecipe['result_count'], 2);
        $profit = round($this->_priceWithoutFee($totalSellPrice) - $optimalCraftCost, 2);

        $this->_dataManager->updateOptimalCraft($originalItemID, $optimalCraftCost, $profit, $type, $date);
    }

    public function detailItem(int $ID): void
    {
        $hierarchyResult = $this->_dataManager->findHierarchyPrices($ID);
        if (!count($hierarchyResult)) throw new \Exception(sprintf('Данные для ID=%s не найдены.', $ID));

        $item = $this->_dataManager->findOneItem($ID);
        $itemPrice = $this->_dataManager->findOnePrice($ID);
        $resultCount = count($hierarchyResult);
        $parentItemID = 0;
        $queue = [];
        $tmpQueue = [];
        $result = [];
        for ($i = 0; $i < $resultCount; ++$i) {
            if ($hierarchyResult[$i]['r_item_id'] === $ID && $hierarchyResult[$i]['r_parent_item_id'] === $parentItemID) {
                if ($hierarchyResult[$i]['i_category'] !== CategoryID::Resources->value) {
                    $tmpQueue[] = [
                        'item' => $hierarchyResult[$i],
                        'r_item_id' => $hierarchyResult[$i]['r_item_id'],
                        'ri_item_id' => $hierarchyResult[$i]['ri_item_id'],
                    ];
                }
            }//end if

            if ($i + 1 >= $resultCount) {
                //todo: Нужно добавить порядок сортировки. Сейчас в выборке обратная сортировка относительно crossoutdb.com.
                foreach ($tmpQueue as $tmpValue) {
                    $queue[] = $tmpValue;
                }
                $tmpQueue = [];
                if (count($queue)) {
                    $_item = array_pop($queue);
                    $ID = $_item['ri_item_id'];
                    $parentItemID = $_item['r_item_id'];
                    $i = -1;
                    $result[] = $_item['item'];
                }
            }
        }//end for

        $separator = str_repeat('-', 64) . PHP_EOL;
        echo $separator;
        foreach ($result as $data) {
            echo vsprintf('|%s %s | %s | %s | %s | %s |' . PHP_EOL, [
                str_repeat('-----', $data['level']),
                $data['ri_item_id'],
                $data['i_name'],
                $data['p_optimal_craft_cost'],
                $data['p_min_buy_price'],
                $data['p_type'],
            ]);
        }
        echo $separator;
        echo sprintf("| Item: %s (%s)", $item['name'], $item['id']) . PHP_EOL;
        echo $separator;
        echo vsprintf('| Profit: %s, %s/%s(%s)' . PHP_EOL, [
            $itemPrice['c_profit'],
            $itemPrice['c_optimal_craft_cost'],
            $this->_priceWithoutFee($itemPrice['max_sell_price']),
            $itemPrice['max_sell_price'],
        ]);
        echo $separator;
    }

    private function _priceWithoutFee(float $price): float
    {
        return round($price * (1 - $this->_fee), 2);
    }
}