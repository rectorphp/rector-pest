<?php

declare(strict_types=1);

use Rector\Pest\PestCollector;
use Rector\Pest\Rector\Class_\CustomTestCaseToUsesRector;
use Rector\Pest\Rector\Class_\PhpDocGroupOnClassToFileScopeGroupRector;
use Rector\Pest\Rector\Class_\TraitUsesToUsesRector;
use Rector\Pest\Rector\ClassMethod\TestClassMethodToPestTestFuncCallRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(TestClassMethodToPestTestFuncCallRector::class);

    $services->set(TraitUsesToUsesRector::class);

    $services->set(PhpDocGroupOnClassToFileScopeGroupRector::class);

    $services->set(DataProviderRector::class);

    $services->set(CustomTestCaseToUsesRector::class);

    $services->set(RemovePHPUnitClassRector::class);

    $services->set(PestCollector::class);
};
