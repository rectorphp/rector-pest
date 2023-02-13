<?php

declare(strict_types=1);

namespace Rector\Pest\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Pest\NodeFactory\PestFuncCallFactory;
use Rector\Pest\ValueObject\MethodCallWithPosition;
use Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer;
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
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Change PHPUnit test method to Pest test function', [
            new CodeSample(
                <<<'CODE_SAMPLE'
// before
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
// after
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

        foreach ($node->getMethods() as $classMethod) {
            if (! $this->testsNodeAnalyzer->isTestClassMethod($classMethod)) {
                continue;
            }

            dump(12345);
            die;
        }

        die;
    }

    private function classMethodRefactor(ClassMethod $classMethod): ?Node
    {
        // @todo move comelte to epst nfoact fyotry
        $pestTestNode = $this->pestFuncCallFactory->create($classMethod);
        $pestTestNode = $this->migratePhpDocGroup($classMethod, $pestTestNode);
        $pestTestNode = $this->migrateDataProvider($classMethod, $pestTestNode);
        $pestTestNode = $this->migrateExpectException($classMethod, $pestTestNode);
        $pestTestNode = $this->migrateSkipCall($classMethod, $pestTestNode);

        return $this->migratePhpDocDepends($classMethod, $pestTestNode);
    }

    /**
     * @return string[]
     */
    private function resolvePhpDocGroupNames(Node $node): array
    {
        $phpDocInfo = $node->getAttribute(AttributeKey::PHP_DOC_INFO);
        if (! $phpDocInfo instanceof PhpDocInfo) {
            return [];
        }

        return array_map(
            static fn (PhpDocTagNode $phpDocTagNode): string => (string) $phpDocTagNode->value,
            $phpDocInfo->getTagsByName('group')
        );
    }

    /**
     * @return string[]
     */
    private function resolvePhpDocDependsNames(Node $node): array
    {
        $phpDocInfo = $node->getAttribute(AttributeKey::PHP_DOC_INFO);
        if (! $phpDocInfo instanceof PhpDocInfo) {
            return [];
        }

        $dependsPhpDocTagNodes = $phpDocInfo->getTagsByName('depends');

        return array_map(
            static fn (PhpDocTagNode $phpDocTagNode): string => (string) $phpDocTagNode->value,
            $dependsPhpDocTagNodes
        );
    }

    private function getExpectExceptionCall(ClassMethod $classMethod): ?MethodCallWithPosition
    {
        foreach ((array) $classMethod->getStmts() as $key => $stmt) {
            if (! $stmt instanceof Expression) {
                continue;
            }

            if (! $stmt->expr instanceof MethodCall) {
                continue;
            }

            if ($this->isMethodCallNamed($stmt->expr, 'this', 'expectException')) {
                /** @var MethodCall $methodCall */
                $methodCall = $stmt->expr;
                return new MethodCallWithPosition($key, $methodCall);
            }
        }

        return null;
    }

    private function migrateExpectException(
        ClassMethod $classMethod,
        FuncCall|MethodCall $pestTestMethodCall
    ): FuncCall|MethodCall {
        $methodCallWithPosition = $this->getExpectExceptionCall($classMethod);

        if ($methodCallWithPosition !== null) {
            $this->removeStmt($pestTestMethodCall->args[1]->value, $methodCallWithPosition->getPosition());

            $pestTestMethodCall = new MethodCall($pestTestMethodCall, 'throws', $methodCallWithPosition->getArgs());
        }

        return $pestTestMethodCall;
    }

    private function migrateDataProvider(
        ClassMethod $classMethod,
        FuncCall|MethodCall $pestTestNode
    ): FuncCall|MethodCall {
        $dataProvider = $this->getDataProviderName($classMethod);
        if ($dataProvider !== null) {
            return new MethodCall($pestTestNode, 'with', [$dataProvider]);
        }

        return $pestTestNode;
    }

    private function migratePhpDocGroup(
        ClassMethod $classMethod,
        FuncCall|MethodCall $pestTestNode
    ): FuncCall|MethodCall {
        $groups = $this->resolvePhpDocGroupNames($classMethod);
        if ($groups !== []) {
            return new MethodCall($pestTestNode, 'group', $groups);
        }

        return $pestTestNode;
    }

    private function migratePhpDocDepends(
        ClassMethod $classMethod,
        FuncCall|MethodCall $pestTestNode
    ): FuncCall|MethodCall {
        $depends = $this->resolvePhpDocDependsNames($classMethod);
        if ($depends !== []) {
            $args = $this->nodeFactory->createArgs($depends);
            return new MethodCall($pestTestNode, 'depends', $depends, $args);
        }

        return $pestTestNode;
    }

    private function getMarkTestSkippedCall(ClassMethod $classMethod): ?MethodCallWithPosition
    {
        foreach ((array) $classMethod->getStmts() as $key => $stmt) {
            if ($stmt->expr !== null && $this->isMethodCallNamed($stmt->expr, 'this', 'markTestSkipped')) {
                /** @var int $key */
                /** @var MethodCall $methodCall */
                $methodCall = $stmt->expr;
                return new MethodCallWithPosition($key, $methodCall);
            }
        }

        return null;
    }

    private function migrateSkipCall(ClassMethod $classMethod, FuncCall|MethodCall $pestTestNode): FuncCall|MethodCall
    {
        $methodCallWithPosition = $this->getMarkTestSkippedCall($classMethod);
        if ($methodCallWithPosition !== null) {
            $this->removeStmt($this->getPestClosure($pestTestNode), $methodCallWithPosition->getPosition());
            return new MethodCall($pestTestNode, 'skip', $methodCallWithPosition->getArgs());
        }

        return $pestTestNode;
    }

    private function isMethodCallNamed(Expr $expr, string $variableName, string $methodName): bool
    {
        if (! $expr instanceof MethodCall) {
            return false;
        }

        if (! $this->isName($expr->var, $variableName)) {
            return false;
        }

        return $this->isName($expr->name, $methodName);
    }
}
