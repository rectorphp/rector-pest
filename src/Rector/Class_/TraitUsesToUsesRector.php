<?php

declare(strict_types=1);

namespace Rector\Pest\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\TraitUse;
use Rector\Core\Rector\AbstractRector;
use Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\Pest\Tests\Rector\Class_\TraitUsesToUsesRector\TraitUsesToUsesRectorTest
 */
final class TraitUsesToUsesRector extends AbstractRector
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
     * @return Stmt[]|null
     */
    public function refactor(Node $node): ?array
    {
        if (! $this->testsNodeAnalyzer->isInTestClass($node)) {
            return null;
        }

        if ($node->getTraitUses() === []) {
            return null;
        }

        $funcCall = $this->createPestUsesFuncCall($node->getTraitUses());

        // remove trait uses
        foreach ($node->stmts as $key => $classStmt) {
            if (! $classStmt instanceof TraitUse) {
                continue;
            }

            unset($node->stmts[$key]);
        }

        return [new Expression($funcCall), $node];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Move class trait uses to Pest uses() function', [
            new CodeSample(
                <<<'CODE_SAMPLE'
use PHPUnit\Framework\TestCase;

class SomeClass extends TestCase
{
    use SomeTrait;
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use PHPUnit\Framework\TestCase;
uses(SomeTrait::class);

class SomeClass extends TestCase
{
    use SomeTrait;
}
CODE_SAMPLE
            )]);
    }

    /**
     * @param TraitUse[] $traitUses
     */
    private function createPestUsesFuncCall(array $traitUses): FuncCall
    {
        $traitNames = [];
        foreach ($traitUses as $traitUse) {
            foreach ($traitUse->traits as $traitName) {
                $traitNames[] = $traitName;
            }
        }

        $traitConstFetches = [];

        foreach ($traitNames as $traitName) {
            $traitConstFetches[] = new ClassConstFetch($traitName, 'class');
        }

        $traitNamesArgs = $this->nodeFactory->createArgs($traitConstFetches);
        return $this->nodeFactory->createFuncCall('uses', $traitNamesArgs);
    }
}
