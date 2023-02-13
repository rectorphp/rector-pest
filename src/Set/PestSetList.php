<?php

declare(strict_types=1);

namespace Rector\Pest\Set;

use Rector\Set\Contract\SetListInterface;

/**
 * @api
 */
final class PestSetList implements SetListInterface
{
    /**
     * @var string
     */
    public const PHPUNIT_TO_PEST = __DIR__ . '/../../config/sets/phpunit-to-pest.php';

    /**
     * @var string
     */
    public const CODE_QUALITY = __DIR__ . '/../../config/sets/pest-code-quality.php';
}
