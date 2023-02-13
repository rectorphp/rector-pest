<?php

namespace Rector\Pest\Tests\Rector\Class_\PhpDocGroupOnClassToFileScopeGroupRector\Fixture;

use PHPUnit\Framework\TestCase;

/**
 * @group integration
 */
final class TestWithGroup extends TestCase
{
    public function testSimple()
    {
        $this->assertTrue(true);
    }
}

?>
-----
<?php

namespace Rector\Pest\Tests\Rector\Class_\PhpDocGroupOnClassToFileScopeGroupRector\Fixture;

use PHPUnit\Framework\TestCase;
uses()->group('integration');
final class TestWithGroup extends TestCase
{
    public function testSimple()
    {
        $this->assertTrue(true);
    }
}

?>
