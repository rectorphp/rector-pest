<?php
use Rector\Pest\Tests\fixtures\TraitA;
use Rector\Pest\Tests\fixtures\TraitB;
use PHPUnit\Framework\TestCase;

class UsesMultipleLinesTest extends TestCase
{
    use TraitA;
    use TraitB;

    public function testUsesMultipleLines()
    {
        $this->assertTrue(true);
    }
}
?>
-----
<?php
use Rector\Pest\Tests\fixtures\TraitA;
use Rector\Pest\Tests\fixtures\TraitB;
use PHPUnit\Framework\TestCase;
uses(TraitA::class, TraitB::class);

class UsesMultipleLinesTest extends TestCase
{
    public function testUsesMultipleLines()
    {
        $this->assertTrue(true);
    }
}
?>
