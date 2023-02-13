<?php

namespace Rector\Pest\Tests\Rector\ClassMethod\SetUpTearDownToBeforeEachAfterEachRector\Fixture;

use PHPUnit\Framework\TestCase;

class SetUpTest extends TestCase
{
    protected function setUp(): void
    {
        $value = 100;
    }
}


?>
-----
<?php

namespace Rector\Pest\Tests\Rector\ClassMethod\SetUpTearDownToBeforeEachAfterEachRector\Fixture;

use PHPUnit\Framework\TestCase;
beforeEach(function () {
    $value = 100;
});

class SetUpTest extends TestCase
{
}


?>
