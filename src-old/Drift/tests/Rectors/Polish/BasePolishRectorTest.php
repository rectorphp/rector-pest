<?php

declare(strict_types=1);

namespace Rector\Pest\Tests\Rector\Polish;

use Rector\Pest\Tests\BaseRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

abstract class BasePolishRectorTest extends BaseRectorTestCase
{
    protected function provideConfigFileInfo(): ?SmartFileInfo
    {
        return new SmartFileInfo(__DIR__ . '/../../config/polish_rectors.php');
    }
}
