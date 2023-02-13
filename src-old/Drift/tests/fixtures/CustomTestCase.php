<?php

declare(strict_types=1);

namespace Rector\Pest\Tests\Fixture;

use PHPUnit\Framework\TestCase;

class CustomTestCase extends TestCase
{
    public function getPestCreator(): string
    {
        return 'Nuno';
    }
}
