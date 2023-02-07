<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Pest\Set\PestSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(PestSetList::PHPUNIT_TO_PEST);
};
