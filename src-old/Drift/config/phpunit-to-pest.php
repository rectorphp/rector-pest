<?php

declare(strict_types=1);

use Rector\Pest\Pest\PestCollectingRector;
use Rector\Pest\PestCollector;
use Rector\Pest\PHPUnit\Class_\CustomTestCaseToUsesRector;
use Rector\Pest\PHPUnit\Class_\PhpDocGroupOnClassToFileScopeGroupRector;
use Rector\Pest\PHPUnit\Class_\RemovePHPUnitClassRector;
use Rector\Pest\PHPUnit\Class_\TraitUsesToUsesRector;
use Rector\Pest\PHPUnit\ClassMethod\AfterClassToAfterAllRector;
use Rector\Pest\PHPUnit\ClassMethod\BeforeClassToBeforeAllRector;
use Rector\Pest\PHPUnit\ClassMethod\DataProviderRector;
use Rector\Pest\PHPUnit\ClassMethod\HelperMethodRector;
use Rector\Pest\PHPUnit\ClassMethod\MethodToPestTestRector;
use Rector\Pest\PHPUnit\ClassMethod\SetUpToBeforeEachRector;
use Rector\Pest\PHPUnit\ClassMethod\TearDownToAfterEachRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(MethodToPestTestRector::class);

    $services->set(TraitUsesToUsesRector::class);

    $services->set(SetUpToBeforeEachRector::class);

    $services->set(TearDownToAfterEachRector::class);

    $services->set(AfterClassToAfterAllRector::class);

    $services->set(BeforeClassToBeforeAllRector::class);

    $services->set(PhpDocGroupOnClassToFileScopeGroupRector::class);

    $services->set(DataProviderRector::class);

    $services->set(HelperMethodRector::class);

    $services->set(CustomTestCaseToUsesRector::class);

    $services->set(RemovePHPUnitClassRector::class);

    $services->set(PestCollectingRector::class);

    $services->set(PestCollector::class);
};
