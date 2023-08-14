<?php

namespace App\Test\TestInterfaceDefinition;

class TestInterfaceDefinition
{
//    public function __construct(One $target)
//    public function __construct(Two $target)
    public function __construct(TestInterface $target)
    {
        dump($target->hello());
    }
}