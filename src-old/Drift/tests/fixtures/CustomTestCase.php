<?php

declare(strict_types=1);

namespace Rector\Pest\Tests\fixtures;

use PHPUnit\Framework\TestCase;

class CustomTestCase extends TestCase
{
    public function getPestCreator(): string
    {
        return 'Nuno';
    }
}
