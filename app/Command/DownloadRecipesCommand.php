<?php

namespace App\Command;

use App\Service\Downloader;
use App\Service\ProfitCalculator;
use App\Service\ProjectPath;
use App\Types\CategoryID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда должна выполняться после загрузки предметов в бд.
 */
class DownloadRecipesCommand extends Command
{
    protected static $defaultName = 'download_recipes';
    private ProjectPath $_projectPath;
    private \PDO $_pdo;

    public function __construct(ProjectPath $projectPath, \PDO $pdo)
    {
        $this->_projectPath = $projectPath;
        $this->_pdo = $pdo;
        parent::__construct(static::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $query = 'select * from items where category <> :category and craftable = :craftable';
        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':category', CategoryID::Resources->value);
        $stmt->bindValue(':craftable', 1);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $downloader = new Downloader();
        $delay = 250 * 1000;
        $urlPattern = 'https://crossoutdb.com/api/v1/recipe/%s';
        $pathPattern = $this->_projectPath->build('data/recipes', '%s' . '.json');
        $count = 0;
        foreach ($result as $item) {
            $url = sprintf($urlPattern, $item['id']);
            $path = sprintf($pathPattern, $item['id']);

            //todo: Перезапись будет запускаться через ключ.
            if (file_exists($path)) continue;

            $filesize = $downloader->download($url, $path);
            ++$count;
            echo vsprintf('Загружен рецепт ID = %s в файл %s (size: %s).' . PHP_EOL, [
                $item['id'],
                $path,
                $filesize,
            ]);

            usleep($delay);
        }

        echo sprintf('Загрузка рецептов завершена, всего загружено: %s.' . PHP_EOL, $count);

        return 0;
    }
}