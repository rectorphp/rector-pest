<?php
use Rector\Pest\Tests\fixtures\CustomTestCase;

class CustomTestCaseTest extends CustomTestCase
{
    public function testCustomTestCase()
    {
        $this->assertTrue(true);
    }
}
?>
-----
<?php
use Rector\Pest\Tests\fixtures\CustomTestCase;
uses(\Rector\Pest\Tests\fixtures\CustomTestCase::class);
test('testCustomTestCase', function () {
    $this->assertTrue(true);
});
?>
