<?php

namespace App\Commands;

use App\Services\ProjectPath;
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
    private \PDO $_pdo;
    private ProjectPath $_path;

    public function __construct(\PDO $pdo, ProjectPath $path)
    {
        $this->_pdo = $pdo;
        $this->_path = $path;
        parent::__construct(static::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $categories = [
            CategoryID::Cabins->value,
            CategoryID::Weapons->value,
            CategoryID::Hardware->value,
            CategoryID::Movement->value,
        ];

        $total = 0;
        $this->_pdo->beginTransaction();
        foreach ($categories as $category) {
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

        $content = json_decode($json, JSON_UNESCAPED_UNICODE);
        if ($content === null) throw new \Exception(sprintf('Ошибка при обработки json: %s.', json_last_error_msg()));

        $count = 0;
        foreach ($content as $item) {
            if (
                !$item['faction'] ||
                !in_array($item['faction'], $this->_availableFaction)
            ) continue;

            $query = 'insert into items (id, name, category, quality, faction) values (:id, :name, :category, :quality, :faction)';
            $stmt = $this->_pdo->prepare($query);

            $stmt->bindValue(':id', $item['id']);
            $stmt->bindValue(':name', $item['name']);
            $stmt->bindValue(':category', $item['categoryName']);
            $stmt->bindValue(':quality', $item['rarityName']);
            $stmt->bindValue(':faction', $item['faction']);

            $stmt->execute();

            ++$count;
        }

        return $count;
    }
}