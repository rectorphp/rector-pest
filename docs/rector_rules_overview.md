# 8 Rules Overview

## AfterBeforeClassToAfterAllBeforeAllRector

Change `@afterClass/@beforeClass` to `afterAll()/beforeAll()` in Pest

- class: [`Rector\Pest\Rector\ClassMethod\AfterBeforeClassToAfterAllBeforeAllRector`](../src/Rector/ClassMethod/AfterBeforeClassToAfterAllBeforeAllRector.php)

```diff
 use PHPUnit\Framework\TestCase;

+afterAll(function () {
+    echo 'afterAll';
+});
+
 class AfterClassTest extends TestCase
 {
-    /**
-     * @afterClass
-     */
-    public function after()
-    {
-        echo 'afterAll';
-    }
 }
```

<br>

## CustomTestCaseToUsesRector

Change parent test case class to `uses()` in Pest

- class: [`Rector\Pest\Rector\Class_\CustomTestCaseToUsesRector`](../src/Rector/Class_/CustomTestCaseToUsesRector.php)

```diff
-use Tests\AbstractCustomTestCase;
+uses(Tests\AbstractCustomTestCase::class);

-class CustomTestCaseTest extends AbstractCustomTestCase
+class CustomTestCaseTest
 {
     public function testCustomTestCase()
     {
     }
 }
```

<br>

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

## PestItNamingRector

Renames tests starting with `it` to use the `it()` function

- class: [`Rector\Pest\Rector\FuncCall\PestItNamingRector`](../src/Rector/FuncCall/PestItNamingRector.php)

```diff
-test('it starts with it')->skip();
+it('starts with it')->skip();
```

<br>

## PhpDocGroupOnClassToFileScopeGroupRector

Changes `@group` phpdoc to `uses()->group()` in Pest

- class: [`Rector\Pest\Rector\Class_\PhpDocGroupOnClassToFileScopeGroupRector`](../src/Rector/Class_/PhpDocGroupOnClassToFileScopeGroupRector.php)

```diff
 use PHPUnit\Framework\TestCase;

-/**
- * @group testGroup
- */
+uses()->group('testGroup');
+
 class SomeClassTest extends TestCase
 {
 }
```

<br>

## SetUpTearDownToBeforeEachAfterEachRector

Change `setUp()` class method to `beforeEach()` func call

- class: [`Rector\Pest\Rector\ClassMethod\SetUpTearDownToBeforeEachAfterEachRector`](../src/Rector/ClassMethod/SetUpTearDownToBeforeEachAfterEachRector.php)

```diff
-use PHPUnit\Framework\TestCase;
-
-class SetUpTest extends TestCase
-{
-    protected function setUp(): void
-    {
-        $value = 100;
-    }
-}
+beforeEach(function () {
+    $value = 100;
+});
```

<br>

## TestClassMethodToPestTestFuncCallRector

Change PHPUnit test method to Pest test function

- class: [`Rector\Pest\Rector\ClassMethod\TestClassMethodToPestTestFuncCallRector`](../src/Rector/ClassMethod/TestClassMethodToPestTestFuncCallRector.php)

```diff
-use PHPUnit\Framework\TestCase;
-
-class ExampleTest extends TestCase
-{
-    public function testSimple()
-    {
-        $this->assertTrue(true);
-    }
-}
+test('testSimple', function () {
+    $this->assertTrue(true);
+});
```

<br>

## TraitUsesToUsesRector

Move class trait uses to Pest `uses()` function

- class: [`Rector\Pest\Rector\Class_\TraitUsesToUsesRector`](../src/Rector/Class_/TraitUsesToUsesRector.php)

```diff
 use PHPUnit\Framework\TestCase;
+uses(SomeTrait::class);

 class SomeClass extends TestCase
 {
     use SomeTrait;
 }
```

<br>
