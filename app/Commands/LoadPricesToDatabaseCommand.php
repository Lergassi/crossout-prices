<?php

namespace App\Commands;

use App\Services\DataManager;
use App\Services\Loader;
use App\Services\PriceController;
use App\Services\ProjectPath;
use PHPHtmlParser\Dom;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

//todo: Сделать обновление цен. Сейчас только создание.
class LoadPricesToDatabaseCommand extends Command
{
    protected static $defaultName = 'db.load_prices';
    private ProjectPath $_projectPath;
    private Loader $_loader;
    private \PDO $_pdo;
    private DataManager $_dataManager;

    public function __construct(ProjectPath $projectPath, Loader $loader, \PDO $pdo, DataManager $dataManager)
    {
        $this->_projectPath = $projectPath;
        $this->_loader = $loader;
        $this->_pdo = $pdo;
        $this->_dataManager = $dataManager;
        parent::__construct(static::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = 'prices.html';
        $path = $this->_projectPath->build('data/prices', $filename);
        $html = $this->_loader->load($path);

        echo sprintf('Начало обработки %s...' . PHP_EOL, $filename);
        $prices = $this->_parseHtmlPrices($html);
        echo sprintf('Обработка %s завершена.' . PHP_EOL, $filename);

        $this->_pdo->beginTransaction();

        $insertPriceQuery = 'insert into prices (max_sell_price, min_buy_price, item_id) values (:max_sell_price, :min_buy_price, :item_id)';

        $insertPriceStmt = $this->_pdo->prepare($insertPriceQuery);

        foreach ($prices as $price) {
            if (!$this->_dataManager->hasItem($price['id'])) continue;

            $insertPriceStmt->bindValue(':max_sell_price', $price['max_sell_price']);
            $insertPriceStmt->bindValue(':min_buy_price', $price['min_buy_price']);
            $insertPriceStmt->bindValue(':item_id', $price['id']);
            $insertPriceStmt->execute();
        }

        $this->_pdo->commit();

        echo 'Цена загружены в базу данных.' . PHP_EOL;

        return 0;
    }

    private function _parseHtmlPrices(string $html): array
    {
        $dom = new Dom();
        $dom->loadStr($html);
        $elements = $dom->find('#ItemTable tbody tr');
        $prices = [];
        foreach ($elements as $element) {
            $tdCollection = $element->find('td');
            $id = intval($tdCollection[2]->text);
            $prices[$id] = [
                'max_sell_price' => floatval($tdCollection[0]->text),
                'min_buy_price' => floatval($tdCollection[1]->text),
                'id' => $id,
            ];
        }

        return $prices;
    }
}