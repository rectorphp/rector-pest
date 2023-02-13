<?php

declare(strict_types=1);

namespace Rector\Pest\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Expression;
use Rector\Core\Rector\AbstractRector;
use Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\Pest\Tests\Rector\Class_\CustomTestCaseToUsesRector\CustomTestCaseToUsesRectorTest
 */
final class CustomTestCaseToUsesRector extends AbstractRector
{
    public function __construct(
        private readonly TestsNodeAnalyzer $testsNodeAnalyzer
    ) {
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

        if (! $node->extends instanceof Name) {
            return null;
        }

        // skip direct classes
        if ($this->isName($node->extends, ' PHPUnit\Framework\TestCase')) {
            return null;
        }

        /** @var string $parentTestCaseName */
        $parentTestCaseName = $this->getName($node->extends);
        $node->extends = null;

        $usesFuncCall = $this->createUsesFuncCall($parentTestCaseName);

        return [new Expression($usesFuncCall), $node];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Change parent test case class to uses() in Pest', [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Tests\AbstractCustomTestCase;

class CustomTestCaseTest extends AbstractCustomTestCase
{
    public function testCustomTestCase()
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
uses(Tests\AbstractCustomTestCase::class);

class CustomTestCaseTest
{
    public function testCustomTestCase()
    {
    }
}
CODE_SAMPLE
            )]);
    }

    private function createUsesFuncCall(string $parentTestCaseName): FuncCall
    {
        $parentReferenceClassConstFetch = new ClassConstFetch(new FullyQualified($parentTestCaseName), 'class');

        return new FuncCall(new Name('uses'), [new Arg($parentReferenceClassConstFetch)]);
    }
}
