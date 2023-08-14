<?php

namespace App\Strategy;

use App\Interface\LoadPricesStrategyInterface;
use App\Service\Loader;
use App\Service\ProjectPath;
use PHPHtmlParser\Dom;

class LoadExportPricesStrategy implements LoadPricesStrategyInterface
{
    private ProjectPath $projectPath;
    private Loader $loader;

    public function __construct(ProjectPath $projectPath, Loader $loader)
    {
        $this->projectPath = $projectPath;
        $this->loader = $loader;
    }

    public function load(): array
    {
//        dump(__CLASS__);
        $filename = 'data/prices.html';
        $path = $this->projectPath->build($filename);
        $html = $this->loader->load($path);

        echo sprintf('Начало обработки %s...' . PHP_EOL, $filename);
        $prices = $this->parseHtmlPrices($html);
        echo sprintf('Обработка %s завершена.' . PHP_EOL, $filename);

        return $prices;
    }

    private function parseHtmlPrices(string $html): array
    {
        $dom = new Dom();
        $dom->loadStr($html);
        $elements = $dom->find('#ItemTable tbody tr');
        $prices = [];
        foreach ($elements as $element) {
            $tdCollection = $element->find('td');
            $id = intval($tdCollection[2]->text);
            $prices[] = [
                'id' => $id,
                'formatSellPrice' => floatval($tdCollection[0]->text),
                'formatBuyPrice' => floatval($tdCollection[1]->text),
            ];
        }

        return $prices;
    }
}