<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Pest\Rector\Class_\PHPUnitTestToPestTestFunctionsRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->rule(PHPUnitTestToPestTestFunctionsRector::class);
};
