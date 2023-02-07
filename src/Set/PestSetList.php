<?php

declare(strict_types=1);

namespace Rector\Pest\Set;

use Rector\Set\Contract\SetListInterface;

final class PestSetList implements SetListInterface
{
    /**
     * @var string
     */
    public const PHPUNIT_TO_PEST = __DIR__ . '/../../config/sets/phpunit-to-pest.php';
}
