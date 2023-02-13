<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Pest\Rector\Class_\CustomTestCaseToUsesRector;
use Rector\Pest\Rector\Class_\PhpDocGroupOnClassToFileScopeGroupRector;
use Rector\Pest\Rector\Class_\PHPUnitTestToPestTestFunctionsRector;
use Rector\Pest\Rector\Class_\TraitUsesToUsesRector;
use Rector\Pest\Rector\ClassMethod\AfterBeforeClassToAfterAllBeforeAllRector;
use Rector\Pest\Rector\ClassMethod\SetUpTearDownToBeforeEachAfterEachRector;

/**
 * @see https://github.com/pestphp/drift
 * @see https://github.com/mandisma/pest-converter
 */
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rules([
        CustomTestCaseToUsesRector::class,
        PhpDocGroupOnClassToFileScopeGroupRector::class,
        PHPUnitTestToPestTestFunctionsRector::class,
        TraitUsesToUsesRector::class,
        AfterBeforeClassToAfterAllBeforeAllRector::class,
        SetUpTearDownToBeforeEachAfterEachRector::class,
    ]);
};
