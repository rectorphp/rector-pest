<?php

declare(strict_types=1);

namespace Rector\Pest\Tests\Rector\PHPUnit;

use Rector\Pest\Tests\BaseRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

abstract class BasePHPUnitRectorTest extends BaseRectorTestCase
{
    protected function provideConfigFileInfo(): ?SmartFileInfo
    {
        return new SmartFileInfo(__DIR__ . '/../../config/phpunit_rectors.php');
    }
}
