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
    public function test(string $filePath): void
    {
        $this->doTestFileInfoWithoutAutoload($fileInfo);
    }

    public static function provideData(): Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    protected function getRectorClass(): string
    {
        return PestTestNamingRector::class;
    }
}
