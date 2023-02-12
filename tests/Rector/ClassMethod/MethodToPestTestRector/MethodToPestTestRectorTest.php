<?php

declare(strict_types=1);

namespace Pest\Drift\Testing\Rectors\PHPUnit\ClassMethod;

use Iterator;
use Pest\Drift\Testing\Rectors\PHPUnit\BasePHPUnitRectorTest;
use Symplify\SmartFileSystem\SmartFileInfo;

final class MethodToPestTestRectorTest extends BasePHPUnitRectorTest
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
            __DIR__ . '/../../../fixtures/PHPUnit/ClassMethod/MethodToPestTestRector'
        );
    }
}
