<?php

declare(strict_types=1);

namespace Rector\Pest\Tests\Rector\Polish\FuncCall;

use Iterator;
use Rector\Pest\Pest\FuncCall\PestItNamingRector;
use Rector\Pest\Tests\Rector\Polish\BasePolishRectorTest;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PestItNamingRectorTest extends BasePolishRectorTest
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
        return $this->yieldFilesFromDirectory(__DIR__ . '/../../../fixtures/Polish/FuncCall/PestItNamingRector');
    }

    protected function getRectorClass(): string
    {
        return PestItNamingRector::class;
    }
}
