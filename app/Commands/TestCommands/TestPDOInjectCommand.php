<?php

namespace App\Commands\TestCommands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestPDOInjectCommand extends Command
{
    protected static $defaultName = 'test.pdo_inject';
    private \PDO $_pdo;

    public function __construct(\PDO $pdo)
    {
        $this->_pdo = $pdo;
        parent::__construct(static::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        dump($this->_pdo);
        $stmt = $this->_pdo->prepare('select * from test_table');
        $stmt->execute();
        dump(count($stmt->fetchAll()) === 3);

        return 0;
    }
}