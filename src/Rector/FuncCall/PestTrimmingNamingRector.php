<?php

declare(strict_types=1);

namespace Rector\Pest\Rector\FuncCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Scalar\String_;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\Pest\Tests\Rector\FuncCall\PestItNamingRector\PestItNamingRectorTest
 */
final class PestTrimmingNamingRector extends AbstractRector implements DocumentedRuleInterface
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Removes spaces at the start and end of the test name', [
            new CodeSample(
                <<<'PHP'
test('  has spaces')->skip();
PHP
                ,
                <<<'PHP'
test('has spaces')->skip();
PHP
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @param FuncCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isNames($node, ['test', 'it', 'todo'])) {
            return null;
        }

        $args = (array) $node->args;
        if ($args === []) {
            return null;
        }

        $firstArgument = $args[0];

        if (! $firstArgument instanceof Arg) {
            return null;
        }

        $value = $firstArgument->value;
        if (! $value instanceof String_) {
            return null;
        }

        $firstArgumentValue = $value->value;

        if (trim($firstArgumentValue) === $firstArgumentValue) {
            return null;
        }

        $firstArgument->value = new String_(trim($firstArgumentValue));

        return $node;
    }
}
