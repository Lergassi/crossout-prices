<?php

namespace App\Command;

use App\Service\DataManager;
use App\Service\Loader;
use App\Service\PriceController;
use App\Service\ProjectPath;
use App\Service\Serializer;
use PHPHtmlParser\Dom;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdatePricesInDatabaseCommand extends Command
{
    protected static $defaultName = 'db.update_prices';
    protected static $defaultDescription = 'Обновляет цены из уже загруженного prices.html.';

    private ProjectPath $_projectPath;
    private Loader $_loader;
    private \PDO $_pdo;
    private DataManager $_dataManager;
    private Serializer $_serializer;

    public function __construct(ProjectPath $projectPath, Loader $loader, \PDO $pdo, DataManager $dataManager, \App\Service\Serializer $serializer)
    {
        parent::__construct(static::$defaultName);
        $this->_projectPath = $projectPath;
        $this->_loader = $loader;
        $this->_pdo = $pdo;
        $this->_dataManager = $dataManager;
        $this->_serializer = $serializer;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        echo 'Начало загрузки цен в бд...' . PHP_EOL;

        $filename = 'prices.html';
        $path = $this->_projectPath->build('data', $filename);
        $html = $this->_loader->load($path);

        echo sprintf('Начало обработки %s...' . PHP_EOL, $filename);
        $prices = $this->_parseHtmlPrices($html);
        echo sprintf('Обработка %s завершена.' . PHP_EOL, $filename);

        $manualPrices = $this->_serializer->decode($this->_loader->load($this->_projectPath->build('data/prices.json')), true);
        $prices = array_merge($prices, $manualPrices);

        $insertPriceQuery = 'insert into prices (max_sell_price, min_buy_price, item_id) values (:max_sell_price, :min_buy_price, :item_id)';
        $selectPriceQuery = 'select * from prices where item_id = :item_id';
        $updatePriceQuery = 'update prices set max_sell_price = :max_sell_price, min_buy_price = :min_buy_price where item_id = :item_id';

        $insertPriceStmt = $this->_pdo->prepare($insertPriceQuery);
        $selectPriceStmt = $this->_pdo->prepare($selectPriceQuery);
        $updatePriceStmt = $this->_pdo->prepare($updatePriceQuery);

        $this->_pdo->beginTransaction();
        foreach ($prices as $price) {
            //todo: Сделать наоборот: получить предметы и искать цену в файле.
            if (!$this->_dataManager->hasItem($price['id'])) continue;

            $selectPriceStmt->bindValue(':item_id', $price['id']);
            $selectPriceStmt->execute();
            $existsPrice = $selectPriceStmt->fetch();

            if (!$existsPrice) {
                $insertPriceStmt->bindValue(':max_sell_price', $price['max_sell_price']);
                $insertPriceStmt->bindValue(':min_buy_price', $price['min_buy_price']);
                $insertPriceStmt->bindValue(':item_id', $price['id']);
                $insertPriceStmt->execute();
            } else {
                $updatePriceStmt->bindValue(':max_sell_price', $price['max_sell_price']);
                $updatePriceStmt->bindValue(':min_buy_price', $price['min_buy_price']);
                $updatePriceStmt->bindValue(':item_id', $price['id']);
                $updatePriceStmt->execute();
            }
        }
        $this->_pdo->commit();

        echo 'Цена загружены в бд.' . PHP_EOL;

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
            $prices[] = [
                'id' => $id,
                'max_sell_price' => floatval($tdCollection[0]->text),
                'min_buy_price' => floatval($tdCollection[1]->text),
            ];
        }

        return $prices;
    }
}