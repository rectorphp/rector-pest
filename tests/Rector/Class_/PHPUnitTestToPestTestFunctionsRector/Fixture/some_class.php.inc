<?php

namespace Rector\Pest\Tests\Rector\Class_\PHPUnitTestToPestTestFunctionsRector\Fixture;

use PHPUnit\Framework\TestCase;

final class SomeTest extends TestCase
{
    public function testSummary()
    {
        $result = 100 + 50;
        $this->assertSame(150, $result);
    }
}

?>
-----
<?php

namespace Rector\Pest\Tests\Rector\Class_\PHPUnitTestToPestTestFunctionsRector\Fixture;

use PHPUnit\Framework\TestCase;

test('testSummary', function () {
    $result = 100 + 50;
    expect($result)->toBe(150);
});

?>
