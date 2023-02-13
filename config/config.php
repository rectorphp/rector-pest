<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $services = $rectorConfig->services();

    $rectorConfig->removeUnusedImports();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Rector\\Pest\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/Set', __DIR__ . '/../src/Rector']);
};
