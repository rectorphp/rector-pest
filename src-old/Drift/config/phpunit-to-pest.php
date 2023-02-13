<?php

declare(strict_types=1);

use Rector\Pest\Pest\PestCollectingRector;
use Rector\Pest\PestCollector;
use Rector\Pest\Rector\Class_\CustomTestCaseToUsesRector;
use Rector\Pest\Rector\Class_\PhpDocGroupOnClassToFileScopeGroupRector;
use Rector\Pest\Rector\Class_\RemovePHPUnitClassRector;
use Rector\Pest\Rector\Class_\TraitUsesToUsesRector;
use Rector\Pest\Rector\ClassMethod\AfterBeforeClassToAfterAllBeforeAllRector;
use Rector\Pest\Rector\ClassMethod\BeforeClassToBeforeAllRector;
use Rector\Pest\Rector\ClassMethod\DataProviderRector;
use Rector\Pest\Rector\ClassMethod\HelperMethodRector;
use Rector\Pest\Rector\ClassMethod\TestClassMethodToPestTestFuncCallRector;
use Rector\Pest\Rector\ClassMethod\SetUpTearDownToBeforeEachAfterEachRector;
use Rector\Pest\Rector\ClassMethod\TearDownToAfterEachRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(TestClassMethodToPestTestFuncCallRector::class);

    $services->set(TraitUsesToUsesRector::class);

    $services->set(SetUpTearDownToBeforeEachAfterEachRector::class);

    $services->set(TearDownToAfterEachRector::class);

    $services->set(AfterBeforeClassToAfterAllBeforeAllRector::class);

    $services->set(BeforeClassToBeforeAllRector::class);

    $services->set(PhpDocGroupOnClassToFileScopeGroupRector::class);

    $services->set(DataProviderRector::class);

    $services->set(HelperMethodRector::class);

    $services->set(CustomTestCaseToUsesRector::class);

    $services->set(RemovePHPUnitClassRector::class);

    $services->set(PestCollectingRector::class);

    $services->set(PestCollector::class);
};
