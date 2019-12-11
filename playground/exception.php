<?php

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

require __DIR__.'/../vendor/autoload.php';

(new \NunoMaduro\Collision\Provider)->register();

class ClassNotFoundException extends Exception implements ProvidesSolution
{
    public function getSolution(): Solution
    {
        return BaseSolution::create('A class import is missing')
            ->setSolutionDescription('You have a missing class import. Try importing this class: App\Post.');
    }
}

throw new ClassNotFoundException('Class `Post` not found.');
