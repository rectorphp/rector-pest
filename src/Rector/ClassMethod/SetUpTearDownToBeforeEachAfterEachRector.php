<?php

declare(strict_types=1);

namespace Rector\Pest\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use Rector\Core\Rector\AbstractRector;
use Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\Pest\Tests\Rector\ClassMethod\SetUpTearDownToBeforeEachAfterEachRector\SetUpTearDownToBeforeEachAfterEachRectorTest
 */
final class SetUpTearDownToBeforeEachAfterEachRector extends AbstractRector
{
    /**
     * @var array<string, string>
     */
    private const METHOD_NAMES_TO_FUNCTION_NAMES = [
        'setUp' => 'beforeEach',
        'tearDown' => 'afterEach',
    ];

    public function __construct(
        private readonly TestsNodeAnalyzer $testsNodeAnalyzer
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Change setUp() class method to beforeEach() func call',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use PHPUnit\Framework\TestCase;

class SetUpTest extends TestCase
{
    protected function setUp(): void
    {
        $value = 100;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
beforeEach(function () {
    $value = 100;
});
CODE_SAMPLE
            )]
        );
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     * @return Stmt[]|null
     */
    public function refactor(Node $node): ?array
    {
        if (! $this->testsNodeAnalyzer->isInTestClass($node)) {
            return null;
        }

        $funcCallExpressions = [];

        foreach (self::METHOD_NAMES_TO_FUNCTION_NAMES as $methodName => $functionName) {
            $desiredClassMethod = $node->getMethod($methodName);
            if (! $desiredClassMethod instanceof ClassMethod) {
                continue;
            }

            $this->removeNode($desiredClassMethod);
            $funcCallExpressions[] = $this->createFuncCallExpression($desiredClassMethod, $functionName);
        }

        if ($funcCallExpressions === []) {
            return null;
        }

        return [...$funcCallExpressions, ...[$node]];
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
