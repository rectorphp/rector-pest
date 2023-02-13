<?php

declare(strict_types=1);

use Rector\Pest\Pest\FuncCall\PestItNamingRector;
use Rector\Pest\Pest\FuncCall\PestTestNamingRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(PestTestNamingRector::class);

    $services->set(PestItNamingRector::class);
};
