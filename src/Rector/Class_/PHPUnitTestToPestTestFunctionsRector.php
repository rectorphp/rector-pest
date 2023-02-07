<?php

declare(strict_types=1);

namespace Rector\Pest\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PHPUnit\Framework\TestCase;
use Rector\Core\Rector\AbstractRector;
use Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\Pest\Tests\Rector\Class_\PHPUnitTestToPestTestFunctionsRector\PHPUnitTestToPestTestFunctionsRectorTest
 */
final class PHPUnitTestToPestTestFunctionsRector extends AbstractRector implements DocumentedRuleInterface
{
    public function __construct(
        private readonly TestsNodeAnalyzer $testsNodeAnalyzer
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Convert PHPUnit test to Pest test functions', [
            new CodeSample(
                <<<'CODE_SAMPLE'
use PHPUnit\Framework\TestCase;

final class SomeTest extends TestCase
{
    public function test()
    {
        $result = 100 + 50;
        $this->assertSame(150, $result);
    }
}
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
test('test', function () {
    $result = 100 + 50;
    expect($result)->toBe(150);
});
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     * @return Expression[]|null
     */
    public function refactor(Node $node): ?array
    {
        // only direct classes for now
        if (! $node->extends instanceof Name) {
            return null;
        }

        if (! $this->isName($node->extends, TestCase::class)) {
            return null;
        }

        $testFuncCalls = [];

        foreach ($node->getMethods() as $classMethod) {
            if (! $this->testsNodeAnalyzer->isTestClassMethod($classMethod)) {
                continue;
            }

            $testNameString = new String_($classMethod->name->toString());
            $testClosure = $this->createTestClosure($classMethod);

            $testFuncCall = new FuncCall(new Name('test'), [new Arg($testNameString), new Arg($testClosure)]);

            $testFuncCalls[] = new Expression($testFuncCall);
        }

        if ($testFuncCalls === []) {
            return null;
        }

        return $testFuncCalls;
    }

    /**
     * @param Stmt[] $stmts
     * @return Stmt[]
     */
    private function createTestStmts(array $stmts): array
    {
        // replace assertSame
        $this->traverseNodesWithCallable($stmts, function (Node $node) {
            if (! $node instanceof MethodCall) {
                return null;
            }

            if (! $this->isName($node->name, 'assertSame')) {
                return null;
            }

            $funcCall = new FuncCall(new Name('expect'), [$node->args[1]]);

            $toBeMethodCall = new MethodCall($funcCall, new Identifier('toBe'), [$node->args[0]]);

            return $toBeMethodCall;
        });

        return $stmts;
    }

    private function createTestClosure(ClassMethod $classMethod): Closure
    {
        return new Closure([
            'stmts' => $this->createTestStmts((array) $classMethod->stmts),
        ]);
    }
}
