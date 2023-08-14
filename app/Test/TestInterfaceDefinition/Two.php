<?php

namespace App\Test\TestInterfaceDefinition;

use App\Service\Serializer;

class Two implements TestInterface
{
    public function __construct(Serializer $serializer)
    {
        dump($serializer);
    }

    public function hello(): string
    {
        return 'Two';
    }
}