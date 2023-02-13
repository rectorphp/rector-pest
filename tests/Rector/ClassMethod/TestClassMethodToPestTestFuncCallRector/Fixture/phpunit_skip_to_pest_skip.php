<?php
use PHPUnit\Framework\TestCase;

class SkipTest extends TestCase
{
    public function testSkip()
    {
        echo "works";
        $this->markTestSkipped('Not yet finished');
    }
}
?>
