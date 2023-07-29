<?php

namespace App\Command;

use App\Service\DataManager;
use App\Service\Loader;
use App\Service\PriceController;
use App\Service\ProjectPath;
use App\Types\CategoryID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadRecipesToDatabaseCommand extends Command
{
    protected static $defaultName = 'db.load_recipes';
    private ProjectPath $_projectPath;
    private Loader $_loader;
    private \PDO $_pdo;
    private DataManager $_dataManager;

    public function __construct(ProjectPath $projectPath, Loader $loader, \PDO $pdo, DataManager $dataManager)
    {
        parent::__construct(static::$defaultName);
        $this->_projectPath = $projectPath;
        $this->_loader = $loader;
        $this->_pdo = $pdo;
        $this->_dataManager = $dataManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = $this->_dataManager->findCraftableItems();

        $this->_pdo->beginTransaction();

        foreach ($result as $item) {
            $this->_load($item['id']);
        }

        $this->_pdo->commit();

        return 0;
    }

    private function _load(int $ID): void
    {
        $path = $this->_projectPath->build('data/recipes', $ID . '.json');

        $content = $this->_loader->loadJson($path);
        $craftCost = $this->_extractCraftCost($content);

        $insertRecipeQuery = 'insert into recipes (craft_cost, result_count, item_id) values (:craft_cost, :result_count, :item_id)';
        $insertRequireItemsQuery = 'insert into require_items (recipe_id, item_id, item_count) VALUES (:recipe_id, :item_id, :item_count)';

        $insertRecipeStmt = $this->_pdo->prepare($insertRecipeQuery);

        $insertRecipeStmt->bindValue(':craft_cost', $craftCost);
        $insertRecipeStmt->bindValue(':result_count', $content['recipe']['item']['craftingResultAmount']);
        $insertRecipeStmt->bindValue(':item_id', $ID);
        $insertRecipeStmt->execute();

        $recipeID = intval($this->_pdo->lastInsertId());

        $insertRequireItemsStmt = $this->_pdo->prepare($insertRequireItemsQuery);
        foreach ($content['recipe']['ingredients'] as $data) {
            if ($data['id'] === -1) continue;

            $insertRequireItemsStmt->bindValue(':recipe_id', $recipeID);
            $insertRequireItemsStmt->bindValue(':item_id', $data['item']['id']);
            $insertRequireItemsStmt->bindValue(':item_count', $data['number']);

            $insertRequireItemsStmt->execute();
        }
    }

    private function _extractCraftCost(array $data): float
    {
        $craftCost = 0.0;
        foreach ($data['recipe']['ingredients'] as $data) {
            if ($data['id'] === -1) {
                $craftCost = floatval($data['item']['formatBuyPrice']);
                break;
            }
        }

        return $craftCost;
    }
}