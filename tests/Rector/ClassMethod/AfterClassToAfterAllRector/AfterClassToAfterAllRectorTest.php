<?php

declare(strict_types=1);

namespace Rector\Pest\Tests\Rector\PHPUnit\ClassMethod;

use Iterator;
use Rector\Pest\Tests\Rector\PHPUnit\BasePHPUnitRectorTest;
use Symplify\SmartFileSystem\SmartFileInfo;

final class AfterClassToAfterAllRectorTest extends BasePHPUnitRectorTest
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
            __DIR__ . '/../../../fixtures/PHPUnit/ClassMethod/AfterClassToAfterAllRector'
        );
    }
}
