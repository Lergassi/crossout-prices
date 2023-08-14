<?php

namespace App\Strategy;

use App\Interface\LoadPricesStrategyInterface;
use App\Service\Loader;
use App\Service\ProjectPath;
use App\Service\Serializer;

class LoadItemsStrategy implements LoadPricesStrategyInterface
{
    private ProjectPath $projectPath;
    private Loader $loader;
    private Serializer $serializer;

    public function __construct(ProjectPath $projectPath, Loader $loader, \App\Service\Serializer $serializer)
    {
        $this->projectPath = $projectPath;
        $this->loader = $loader;
        $this->serializer = $serializer;
    }

    public function load(): array
    {
//        dump(__CLASS__);
        $filename = 'data/crossoutdb/items.json';
        $path = $this->projectPath->build($filename);
        $json = $this->loader->load($path);
        $prices = $this->serializer->decode($json, true);

        return $prices;
    }
}