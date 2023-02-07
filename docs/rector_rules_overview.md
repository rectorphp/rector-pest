# 1 Rules Overview

## PHPUnitTestToPestTestFunctionsRector

Convert PHPUnit test to Pest test functions

- class: [`Rector\Pest\Rector\Class_\PHPUnitTestToPestTestFunctionsRector`](../src/Rector/Class_/PHPUnitTestToPestTestFunctionsRector.php)

```diff
-use PHPUnit\Framework\TestCase;
-
-final class SomeTest extends TestCase
-{
-    public function test()
-    {
-        $result = 100 + 50;
-        $this->assertSame(150, $result);
-    }
-}
+test('test', function () {
+    $result = 100 + 50;
+    expect($result)->toBe(150);
+});
```

<br>
