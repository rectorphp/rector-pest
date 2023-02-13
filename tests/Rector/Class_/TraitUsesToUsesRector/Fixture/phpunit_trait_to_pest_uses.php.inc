<?php
use Rector\Pest\Tests\fixtures\TraitA;
use PHPUnit\Framework\TestCase;

class UsesTest extends TestCase
{
    use TraitA;

    public function testUses()
    {
        $this->assertTrue(true);
    }
}
?>
-----
<?php
use Rector\Pest\Tests\fixtures\TraitA;
use PHPUnit\Framework\TestCase;
uses(TraitA::class);

class UsesTest extends TestCase
{
    public function testUses()
    {
        $this->assertTrue(true);
    }
}
?>
