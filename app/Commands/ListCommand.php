<?php

namespace App\Commands;

use App\Services\Database;
use App\Services\PriceController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command
{
    protected static $defaultName = 'list';

//    public function __construct(Database $database)
    private PriceController $_priceController;

    public function __construct(PriceController $priceController)
    {
        $this->_priceController = $priceController;
        parent::__construct(static::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_priceController->list();

        return 0;
    }
}