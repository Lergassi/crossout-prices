<?php

namespace App\Commands\SandboxCommands;

use App\Commands\TestCommands\TestInjectContainerCommand;

use App\Services\DataManager;
use App\Test\Foo;
use DI\Container;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class MainSandboxCommand extends Command
{
    protected static $defaultName = 'sandbox';
    private ContainerInterface $_container;
    private \PDO $_pdo;

    public function __construct(ContainerInterface $container)
    {
        $this->_container = $container;
        $this->_pdo = $container->get(\PDO::class);
        parent::__construct(static::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        $this->_devDatabase();
//        $this->_devContainer();
//        $this->_devContainerInject();
//        $this->_devContainerInjectToCommand();
//        $this->_devContainerInjectToCommand();
        $this->_devDataManager();
//        $this->_devMysqlFetchFloat();

        return 0;
    }

    private function _devDatabase()
    {
//        $pdo = new \PDO(
//            sprintf('mysql:host=%s;dbname=%s', $_ENV['APP_DB_HOST'], $_ENV['APP_DB_NAME']),
//            $_ENV['APP_DB_USER'],
//            $_ENV['APP_DB_PASSWORD'],
//            [
//                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
//            ]
//        );
//        $db = new Database(
//            $_ENV['APP_DB_HOST'],
//            $_ENV['APP_DB_NAME'],
//            $_ENV['APP_DB_USER'],
//            $_ENV['APP_DB_PASSWORD'],
//        );
//        dd($db);

        $query =
            /** @lang MySQL */
            'WITH RECURSIVE main_query_02 AS (
            select
                r1.id as r_id,
                r1.item_id as r_item_id,
                ri1.id as ri_id,
                ri1.item_id as ri_item_id,
                ri1.item_count
            from require_items ri1
                left join recipes r1 on r1.id = ri1.recipe_id
            where r1.item_id = 497
            UNION ALL
                select
                    r2.id,
                    r2.item_id,
                    ri2.id,
                    ri2.item_id,
                    ri2.item_count
                from recipes r2
                    left join require_items ri2 on r2.id = ri2.recipe_id
                    left join items i1 on i1.id = r2.item_id,
                    main_query_02 mq02
                where
                    r2.item_id = mq02.ri_item_id
            )
            
            SELECT
                ri_item_id, sum(item_count) as s
            FROM main_query_02
                left join items i on i.id = ri_item_id
            where
                i.category = \'resource\'
            group by ri_item_id
            order by s desc'
        ;

        $stmt = $db->getPdo()->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll();
        dump($result);
    }

    private function _devContainer()
    {
        $containerBuilder = new ContainerBuilder();

//        $containerBuilder->addDefinitions([
//            Database::class => function (ContainerInterface $container) {
//                return new Database(
//                    $_ENV['APP_DB_HOST'],
//                    $_ENV['APP_DB_NAME'],
//                    $_ENV['APP_DB_USER'],
//                    $_ENV['APP_DB_PASSWORD'],
//                );
//            },
//            'host' => 'localhost',
//        ]);

//        $container = new Container();
        $container = $containerBuilder->build();

//        $container->

        $container->set('foo', 'bar');
//        $container->set(Database::class, );

//        dump($container->get('foo'));
//        dump($container->get('host'));
//        dump($container->get(Foo::class));
//        dump($container->get(Database::class));
    }

    private function _devContainerInject()
    {
//        dump($this->_container->get(Database::class));
    }

    private function _devContainerInjectToCommand()
    {
//        dump($this->_container->get(Foo::class));
        /** @var TestInjectContainerCommand $command */
        $command = $this->_container->get(TestInjectContainerCommand::class);
//        dump($command);
        $command->execute(new StringInput(''), new NullOutput());
    }

    private function _devDataManager()
    {
        /** @var DataManager $dataManager */
        $dataManager = $this->_container->get(DataManager::class);

        $ID = 497;
//        $ID = 112;
//        $ID = 108;
//        $ID = 482;
//        $ID = 383;
//        $ID = -1;
//        $ID = 0;
//        $ID = 42;
//        $ID = 1.42;
//        $ID = '';
//        $ID = null;

//        $requireItems = $dataManager->findHierarchyRequireItems($ID);
//        dump($requireItems);
        dump($dataManager->findRequireItems($ID));
    }

    private function _devMysqlFetchFloat()
    {
        $query = 'select * from test_table';
        $stmt = $this->_pdo->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll();
        dump($result);
    }
}