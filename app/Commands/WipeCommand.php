<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WipeCommand extends Command
{
    protected static $defaultName = 'wipe';
    private \PDO $_pdo;

    public function __construct(\PDO $pdo)
    {
        parent::__construct(static::$defaultName);
        $this->_pdo = $pdo;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queries = [
            'SET foreign_key_checks = 0',
            'truncate table prices',
            'truncate table require_items',
            'truncate table recipes',
            'truncate table items',
            'SET foreign_key_checks = 1',
        ];

        foreach ($queries as $query) {
            $stmt = $this->_pdo->prepare($query);
            $stmt->execute();
        }

        return 0;
    }
}