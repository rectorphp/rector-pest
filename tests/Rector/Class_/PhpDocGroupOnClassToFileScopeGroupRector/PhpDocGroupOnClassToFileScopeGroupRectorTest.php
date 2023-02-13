<?php

declare(strict_types=1);

namespace Rector\Pest\Tests\Rector\PHPUnit\Class_;

use Iterator;
use Rector\Pest\Tests\Rector\PHPUnit\BasePHPUnitRectorTest;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PhpDocGroupOnClassToFileScopeGroupRectorTest extends BasePHPUnitRectorTest
{
    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfo($fileInfo);
    }

    public function provideData(): Iterator
    {
        return $this->yieldFilesFromDirectory(
            __DIR__ . '/../../../fixtures/PHPUnit/Class_/PhpDocGroupOnClassToFileScopeGroupRector'
        );
    }
}
