<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Pest\Rector\FuncCall\PestItNamingRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rules([PestItNamingRector::class]);
};
