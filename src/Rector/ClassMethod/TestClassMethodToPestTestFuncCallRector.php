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
     * @return Node\Stmt[]|null
     */
    public function refactor(Node $node): ?array
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
        $pestTestCall = $this->pestFuncCallFactory->create($classMethod);

        // doc block related
        $pestTestCall = $this->refactorPhpDocAnnotationToMethodCall($classMethod, $pestTestCall, 'group');
        $pestTestCall = $this->refactorPhpDocAnnotationToMethodCall($classMethod, $pestTestCall, 'depends');

        foreach ((array) $classMethod->stmts as $classMethodStmt) {
            if (! $classMethodStmt instanceof Expression) {
                continue;
            }

            if (! $classMethodStmt->expr instanceof MethodCall) {
                continue;
            }

            $methodCall = $classMethodStmt->expr;

            // this is important!
            if ($this->isName($methodCall->name, 'expectException')) {
                $pestTestCall = new MethodCall($pestTestCall, 'throws', $methodCall->getArgs());

                $this->removeNode($classMethodStmt);
            }

            if ($this->isName($methodCall->name, 'markTestSkipped')) {
                $pestTestCall = new MethodCall($pestTestCall, 'skip', $methodCall->getArgs());

                $this->removeNode($classMethodStmt);
            }
        }

        return $this->migrateDataProvider($classMethod, $pestTestCall);
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

    private function refactorPhpDocAnnotationToMethodCall(
        ClassMethod $classMethod,
        FuncCall|MethodCall $pestTestNode,
        string $annotationName
    ): FuncCall|MethodCall {
        $groups = $this->phpDocResolver->resolvePhpDocValuesByName($classMethod, $annotationName);

        if ($groups !== []) {
            $args = $this->nodeFactory->createArgs($groups);
            return new MethodCall($pestTestNode, $annotationName, $args);
        }

        return $pestTestNode;
    }
}
