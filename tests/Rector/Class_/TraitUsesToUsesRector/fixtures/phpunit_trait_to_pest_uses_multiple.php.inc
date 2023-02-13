<?php
use Rector\Pest\Tests\fixtures\TraitA;
use PHPUnit\Framework\TestCase;

class UsesMultipleTest extends TestCase
{
    use TraitA, \Rector\Pest\Tests\fixtures\TraitB;

    public function testUsesMultiple()
    {
        $this->assertTrue(true);
    }
}
?>
-----
<?php
use Rector\Pest\Tests\fixtures\TraitA;
use PHPUnit\Framework\TestCase;
uses(TraitA::class, \Rector\Pest\Tests\fixtures\TraitB::class);
test('testUsesMultiple', function () {
    $this->assertTrue(true);
});
?>
