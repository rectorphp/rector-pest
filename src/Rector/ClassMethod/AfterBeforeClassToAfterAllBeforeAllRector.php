<?php

declare(strict_types=1);

namespace Rector\Pest\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use Rector\Core\Rector\AbstractRector;
use Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\Pest\Tests\Rector\ClassMethod\AfterBeforeClassToAfterAllBefoerAllRector\AfterBeforeClassToAfterAllBefoerAllRectorTest
 */
final class AfterBeforeClassToAfterAllBeforeAllRector extends AbstractRector
{
    private const ANNOTATIONS_TO_FUNCTION_NAMES = [
        'beforeClass' => 'beforeAll',
        'afterClass' => 'afterAll',
    ];

    public function __construct(
        private readonly TestsNodeAnalyzer $testsNodeAnalyzer
    ) {
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Change @afterClass/@beforeClass to afterAll()/beforeAll() in Pest', [
            new CodeSample(
                <<<'CODE_SAMPLE'
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
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use PHPUnit\Framework\TestCase;

afterAll(function () {
    echo 'afterAll';
});

class AfterClassTest extends TestCase
{
}
CODE_SAMPLE
            )]);
    }

    /**
     * @param Class_ $node
     * @return Node[]|null
     */
    public function refactor(Node $node)
    {
        if (! $this->testsNodeAnalyzer->isInTestClass($node)) {
            return null;
        }

        $funcCallExpressions = [];

        foreach ($node->getMethods() as $classMethod) {
            if (! $classMethod->isPublic()) {
                continue;
            }

            foreach (self::ANNOTATIONS_TO_FUNCTION_NAMES as $annotationName => $functionName) {
                if (! $this->isAnnotationClassMethod($classMethod, $annotationName)) {
                    continue;
                }

                $funcCallExpressions[] = $this->createFuncCallExpression($classMethod, $functionName);
                $this->removeNode($classMethod);
            }
        }

        if ($funcCallExpressions === []) {
            return null;
        }

        return [...$funcCallExpressions, ...[$node]];
    }

    private function isAnnotationClassMethod(ClassMethod $classMethod, string $annotationName): bool
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($classMethod);
        return $phpDocInfo->hasByName($annotationName);
    }

    private function createFuncCallExpression(ClassMethod $classMethod, string $functionName): Expression
    {
        $funcCall = $this->nodeFactory->createFuncCall($functionName, [
            new Closure([
                'stmts' => $classMethod->stmts,
            ]),
        ]);

        return new Expression($funcCall);
    }
}
