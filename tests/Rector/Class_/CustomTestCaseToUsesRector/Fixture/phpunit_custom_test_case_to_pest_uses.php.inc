<?php

namespace Rector\Pest\Tests\Rector\Class_\CustomTestCaseToUsesRector\Fixture;

use Rector\Pest\Tests\Rector\Class_\CustomTestCaseToUsesRector\Source\AbstractCustomTestCase;

class CustomTestCaseTest extends AbstractCustomTestCase
{
    public function testCustomTestCase()
    {
        $this->assertTrue(true);
    }
}

?>
-----
<?php

namespace Rector\Pest\Tests\Rector\Class_\CustomTestCaseToUsesRector\Fixture;

use Rector\Pest\Tests\Rector\Class_\CustomTestCaseToUsesRector\Source\AbstractCustomTestCase;
uses(\Rector\Pest\Tests\Rector\Class_\CustomTestCaseToUsesRector\Source\AbstractCustomTestCase::class);

class CustomTestCaseTest
{
    public function testCustomTestCase()
    {
        $this->assertTrue(true);
    }
}

?>
