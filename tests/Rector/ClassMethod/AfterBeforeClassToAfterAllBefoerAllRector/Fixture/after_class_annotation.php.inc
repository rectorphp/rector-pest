<?php

namespace Rector\Pest\Tests\Rector\ClassMethod\AfterBeforeClassToAfterAllBefoerAllRector\Fixture;

use PHPUnit\Framework\TestCase;

class AfterClassTest extends TestCase
{
    /**
     * @afterClass
     */
    public function after()
    {
        echo 'afterAll';
    }
}

?>
-----
<?php

namespace Rector\Pest\Tests\Rector\ClassMethod\AfterBeforeClassToAfterAllBefoerAllRector\Fixture;

use PHPUnit\Framework\TestCase;
afterAll(function () {
    echo 'afterAll';
});

class AfterClassTest extends TestCase
{
}

?>
