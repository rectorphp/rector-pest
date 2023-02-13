<?php

declare(strict_types=1);

namespace Rector\Pest\Tests\Rector\Polish\FuncCall;

use Iterator;
use Rector\Pest\Pest\FuncCall\PestTestNamingRector;
use Rector\Pest\Tests\Rector\Polish\BasePolishRectorTest;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PestTestNamingRectorTest extends BasePolishRectorTest
{
    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfoWithoutAutoload($fileInfo);
    }

    public function provideData(): Iterator
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/../../../fixtures/Polish/FuncCall/PestTestNamingRector');
    }

    protected function getRectorClass(): string
    {
        return PestTestNamingRector::class;
    }
}
