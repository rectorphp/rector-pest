<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
<<<<<<< HEAD
use Rector\Pest\Set\PestSetList;
=======
use Rector\PEST\Set\PestSetList;
>>>>>>> 7a06171 (kck off test)

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(PestSetList::PHPUNIT_TO_PEST);
};
