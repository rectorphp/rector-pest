<?php

namespace Rector\Pest\Tests\Rector\ClassMethod\TestClassMethodToPestTestFuncCallRector\Fixture;

use PHPUnit\Framework\TestCase;

final class GroupTest extends TestCase
{
    /**
     * @group integration
     */
    public function testSimple()
    {
        $this->assertTrue(true);
    }
}

?>
-----
<?php

namespace Rector\Pest\Tests\Rector\ClassMethod\TestClassMethodToPestTestFuncCallRector\Fixture;

test('testSimple', function () {
    $this->assertTrue(true);
})->group('integration');

?>
