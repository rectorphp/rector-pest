<?php

namespace Rector\Pest\Tests\Rector\ClassMethod\AfterBeforeClassToAfterAllBefoerAllRector\Fixture;

use PHPUnit\Framework\TestCase;

class BeforeClassTest extends TestCase
{
    /**
     * @beforeClass
     */
    public function before()
    {
        echo 'beforeAll';
    }
}

?>
-----
<?php

namespace Rector\Pest\Tests\Rector\ClassMethod\AfterBeforeClassToAfterAllBefoerAllRector\Fixture;

use PHPUnit\Framework\TestCase;
beforeAll(function () {
    echo 'beforeAll';
});

class BeforeClassTest extends TestCase
{
}

?>
