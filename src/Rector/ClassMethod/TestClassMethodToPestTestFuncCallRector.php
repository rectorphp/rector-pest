<?php

declare(strict_types=1);

namespace Rector\Pest\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use Rector\Core\Rector\AbstractRector;
use Rector\Pest\NodeFactory\PestFuncCallFactory;
use Rector\Pest\PhpDocResolver;
use Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer;
use Rector\PHPUnit\NodeFinder\DataProviderClassMethodFinder;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\Pest\Tests\Rector\ClassMethod\TestClassMethodToPestTestFuncCallRector\TestClassMethodToPestTestFuncCallRectorTest
 */
final class TestClassMethodToPestTestFuncCallRector extends AbstractRector
{
    public function __construct(
        private readonly TestsNodeAnalyzer $testsNodeAnalyzer,
        private readonly PestFuncCallFactory $pestFuncCallFactory,
        private readonly DataProviderClassMethodFinder $dataProviderClassMethodFinder,
        private readonly PhpDocResolver $phpDocResolver,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Change PHPUnit test method to Pest test function', [
            new CodeSample(
                <<<'CODE_SAMPLE'
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testSimple()
    {
        $this->assertTrue(true);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
test('testSimple', function () {
    $this->assertTrue(true);
});
CODE_SAMPLE
            )]);
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     */
    public function refactor(Node $node)
    {
        if (! $this->testsNodeAnalyzer->isInTestClass($node)) {
            return null;
        }

        $testCallExpressions = [];

        foreach ($node->getMethods() as $classMethod) {
            if (! $this->testsNodeAnalyzer->isTestClassMethod($classMethod)) {
                continue;
            }

            $testCall = $this->createPestTestCall($classMethod);
            $testCallExpressions[] = new Expression($testCall);
        }

        if ($testCallExpressions === []) {
            return null;
        }

        return $testCallExpressions;
    }

    private function createPestTestCall(ClassMethod $classMethod): FuncCall|MethodCall
    {
        // @todo move comelte to epst nfoact fyotry
        $pestTestNode = $this->pestFuncCallFactory->create($classMethod);
        $pestTestNode = $this->migratePhpDocGroup($classMethod, $pestTestNode);
        $pestTestNode = $this->migrateDataProvider($classMethod, $pestTestNode);
        $pestTestNode = $this->migrateExpectException($classMethod, $pestTestNode);
        $pestTestNode = $this->migrateSkipCall($classMethod, $pestTestNode);

        return $this->migratePhpDocDepends($classMethod, $pestTestNode);
    }

    private function getExpectExceptionCall(ClassMethod $classMethod): ?MethodCall
    {
        foreach ((array) $classMethod->getStmts() as $stmt) {
            if (! $stmt instanceof Expression) {
                continue;
            }

            if (! $stmt->expr instanceof MethodCall) {
                continue;
            }

            $methodCall = $stmt->expr;
            if (! $this->isName($methodCall->name, 'expectException')) {
                continue;
            }

            return $methodCall;
        }

        return null;
    }

    private function migrateExpectException(
        ClassMethod $classMethod,
        FuncCall|MethodCall $pestTestMethodCall
    ): FuncCall|MethodCall {
        $methodCall = $this->getExpectExceptionCall($classMethod);

        if ($methodCall !== null) {
            $this->removeNode($methodCall);

            return new MethodCall($pestTestMethodCall, 'throws', $methodCall->getArgs());
        }

        return $pestTestMethodCall;
    }

    private function migrateDataProvider(
        ClassMethod $classMethod,
        FuncCall|MethodCall $pestTestNode
    ): FuncCall|MethodCall {
        $dataProviderMethodNames = $this->dataProviderClassMethodFinder->findDataProviderNamesForClassMethod(
            $classMethod
        );

        $dataProviderMethodName = $dataProviderMethodNames[0] ?? null;
        if ($dataProviderMethodName !== null) {
            $args = $this->nodeFactory->createArgs([$dataProviderMethodName]);
            return new MethodCall($pestTestNode, 'with', $args);
        }

        return $pestTestNode;
    }

    private function migratePhpDocGroup(
        ClassMethod $classMethod,
        FuncCall|MethodCall $pestTestNode
    ): FuncCall|MethodCall {
        $groups = $this->phpDocResolver->resolvePhpDocValuesByName($classMethod, 'group');

        if ($groups !== []) {
            $args = $this->nodeFactory->createArgs($groups);
            return new MethodCall($pestTestNode, 'group', $args);
        }

        return $pestTestNode;
    }

    private function migratePhpDocDepends(
        ClassMethod $classMethod,
        FuncCall|MethodCall $pestTestNode
    ): FuncCall|MethodCall {
        $depends = $this->phpDocResolver->resolvePhpDocValuesByName($classMethod, 'depends');
        if ($depends !== []) {
            $args = $this->nodeFactory->createArgs($depends);
            return new MethodCall($pestTestNode, 'depends', $args);
        }

        return $pestTestNode;
    }

    private function getMarkTestSkippedCall(ClassMethod $classMethod): ?MethodCall
    {
        foreach ((array) $classMethod->getStmts() as $stmt) {
            if (! $stmt instanceof Expression) {
                continue;
            }

            if (! $stmt->expr instanceof MethodCall) {
                continue;
            }

            $methodCall = $stmt->expr;
            if (! $this->isName($methodCall->name, 'markTestSkipped')) {
                continue;
            }

            return $methodCall;
        }

        return null;
    }

    private function migrateSkipCall(ClassMethod $classMethod, FuncCall|MethodCall $pestTestNode): FuncCall|MethodCall
    {
        $methodCall = $this->getMarkTestSkippedCall($classMethod);
        if ($methodCall !== null) {
            $this->removeNode($methodCall);
            return new MethodCall($pestTestNode, 'skip', $methodCall->getArgs());
        }

        return $pestTestNode;
    }
}
