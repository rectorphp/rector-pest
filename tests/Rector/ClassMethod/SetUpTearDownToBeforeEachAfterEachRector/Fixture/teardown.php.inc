<?php

namespace Rector\Pest\Tests\Rector\ClassMethod\SetUpTearDownToBeforeEachAfterEachRector\Fixture;

use PHPUnit\Framework\TestCase;

class TearDownTest extends TestCase
{
    protected function tearDown(): void
    {
        echo "works";
    }
}

?>
-----
<?php

namespace Rector\Pest\Tests\Rector\ClassMethod\SetUpTearDownToBeforeEachAfterEachRector\Fixture;

use PHPUnit\Framework\TestCase;
afterEach(function () {
    echo "works";
});

class TearDownTest extends TestCase
{
}

?>
