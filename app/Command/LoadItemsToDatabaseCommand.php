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

    private array $_availableFaction = [
        FactionID::Engineers->value,
        FactionID::Lunatics->value,
        FactionID::Nomads->value,
        FactionID::Scavengers->value,
        FactionID::SteppenWolfs->value,
        FactionID::DawnChildren->value,
        FactionID::FireStarters->value,
    ];
    private array $availableCategories = [
        CategoryID::Cabins->value,
        CategoryID::Weapons->value,
        CategoryID::Hardware->value,
        CategoryID::Movement->value,
        CategoryID::Resources->value,
    ];

    private \PDO $_pdo;
    private ProjectPath $_path;
    private Loader $_loader;
    private Serializer $_serializer;

    public function __construct(\PDO $pdo, ProjectPath $path, Loader $loader, Serializer $serializer)
    {
        parent::__construct(static::$defaultName);
        $this->_pdo = $pdo;
        $this->_path = $path;
        $this->_loader = $loader;
        $this->_serializer = $serializer;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $total = 0;
        $this->_pdo->beginTransaction();
        foreach ($this->availableCategories as $category) {
            $count = $this->_loadCategory($category);
            echo vsprintf('Добавлено записей: %s, для категории: %s', [
                    $count,
                    $category,
                ]) . PHP_EOL;
            $total += $count;
        }

        $this->_pdo->commit();

        echo sprintf('Загрузка данных в бд завершена. Всего добавлено записей: %s.' . PHP_EOL, $total);

        return 0;
    }

    /**
     * todo: Переделать. Загружать нужно из источника (файла), который получается выше - сюда уже строку/объект. И можно будет легко выбрать файл с ручной загрузкой.
     * @param string $category
     * @return int Кол-во добавленных записей.
     * @throws \Exception
     */
    private function _loadCategory(string $category): int
    {
        $filepath = $this->_path->build('data', $category . '.json');
        if (!file_exists($filepath)) throw new \Exception(sprintf('Файл %s не найден.', $filepath));

        $fp = fopen($filepath, 'r');
        $json = fread($fp, filesize($filepath));
        fclose($fp);

        $content = $this->_serializer->decode($json, true);

        $availableCraftItemsJson = $this->_loader->load($this->_path->build('data', 'available_craft_items.json'));
        $availableCraftItems = $this->_serializer->decode($availableCraftItemsJson);

        $query = 'insert into items (id, name, category, quality, faction, craftable, available_craft) values (:id, :name, :category, :quality, :faction, :craftable, :available_craft)';
        $stmt = $this->_pdo->prepare($query);

        $count = 0;
        foreach ($content as $item) {
            $stmt->bindValue(':id', $item['id']);
            $stmt->bindValue(':name', $item['name']);
            $stmt->bindValue(':category', $item['categoryName']);
            $stmt->bindValue(':quality', $item['rarityName']);
            $stmt->bindValue(':faction', $item['faction']);
            $stmt->bindValue(':craftable', intval($item['craftVsBuy'] !== 'Uncraftable'));
            $stmt->bindValue(':available_craft', intval(in_array($item['id'], $availableCraftItems)));

            $stmt->execute();

            ++$count;
        }

        return $count;
    }
}