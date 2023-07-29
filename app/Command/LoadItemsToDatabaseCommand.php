<?php

namespace App\Command;

use App\Service\Loader;
use App\Service\ProjectPath;
use App\Service\Serializer;
use App\Types\CategoryID;
use App\Types\FactionID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadItemsToDatabaseCommand extends Command
{
    protected static $defaultName = 'db.load_items';

    private array $availableCraftItems;

    private \PDO $_pdo;
    private ProjectPath $_path;
    private Loader $_loader;
    private Serializer $_serializer;

    public function __construct(\PDO $pdo, ProjectPath $path, Loader $loader, Serializer $serializer)
    {
        parent::__construct(static::$defaultName);
        $this->availableCraftItems = [];
        $this->_pdo = $pdo;
        $this->_path = $path;
        $this->_loader = $loader;
        $this->_serializer = $serializer;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $itemsDir = 'data/items';
        $manualItemsDir = 'data';
        $paths = [
            $this->_path->build($itemsDir, CategoryID::Cabins->value . '.json'),
            $this->_path->build($itemsDir, CategoryID::Weapons->value . '.json'),
            $this->_path->build($itemsDir, CategoryID::Hardware->value . '.json'),
            $this->_path->build($itemsDir, CategoryID::Movement->value . '.json'),
            $this->_path->build($itemsDir, CategoryID::Resources->value . '.json'),
            $this->_path->build($manualItemsDir, 'items.json'),
        ];

        $availableCraftItemsJson = $this->_loader->load($this->_path->build('data/available_craft_items.json'));
        $this->availableCraftItems = $this->_serializer->decode($availableCraftItemsJson);

        $total = 0;
        $this->_pdo->beginTransaction();
        foreach ($paths as $path) {
            $itemsJson = $this->_loader->load($path);
            $items = $this->_serializer->decode($itemsJson, true);

            $count = $this->_loadCategory($items);
            echo vsprintf('Добавлено записей: %s, аз файла: %s', [
                    $count,
                    $path,
                ]) . PHP_EOL;
            $total += $count;
        }
        $this->_pdo->commit();

        echo sprintf('Загрузка данных в бд завершена. Всего добавлено записей: %s.' . PHP_EOL, $total);

        return 0;
    }

    /**
     * @param string $category
     * @return int Кол-во добавленных записей.
     * @throws \Exception
     */
//    private function _loadCategory(string $category): int
    private function _loadCategory(array $items): int
    {
        $query = 'insert into items (id, name, category, quality, faction, craftable, available_craft) values (:id, :name, :category, :quality, :faction, :craftable, :available_craft)';
        $stmt = $this->_pdo->prepare($query);

        $count = 0;
        foreach ($items as $item) {
            $stmt->bindValue(':id', $item['id']);
            $stmt->bindValue(':name', $item['name']);
            $stmt->bindValue(':category', $item['categoryName']);
            $stmt->bindValue(':quality', $item['rarityName']);
            $stmt->bindValue(':faction', $item['faction']);
            $stmt->bindValue(':craftable', intval($item['craftVsBuy'] !== 'Uncraftable'));
            $stmt->bindValue(':available_craft', $this->isAvailableCraft($item['id']));

            $stmt->execute();

            ++$count;
        }

        return $count;
    }

    private function isAvailableCraft(int $ID): int
    {
        return intval(in_array($ID, $this->availableCraftItems));
    }
}