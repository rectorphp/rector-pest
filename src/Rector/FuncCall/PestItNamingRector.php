<?php

declare(strict_types=1);

namespace Rector\Pest\Rector\FuncCall;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\Pest\Tests\Rector\FuncCall\PestItNamingRector\PestItNamingRectorTest
 */
final class PestItNamingRector extends AbstractRector implements DocumentedRuleInterface
{
    /** @var string */
    private const KEYWORD = 'it';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Renames tests starting with `it` to use the `it()` function', [
            new CodeSample(
                <<<'PHP'
test('it starts with it')->skip();
PHP,
                <<<'PHP'
it('starts with it')->skip();
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
        if (! $this->isNames($node, ['test', 'it'])) {
            return null;
        }

        $args = (array) $node->args;
        if (count($args) === 0) {
            return null;
        }

        $firstArgument = $args[0];

        if (!$firstArgument instanceof Node\Arg) {
            return null;
        }

        $value = $firstArgument->value;
        if (!$value instanceof String_) {
            return null;
        }

        $firstArgumentValue = $value->value;

        if (! Strings::startsWith($firstArgumentValue, self::KEYWORD)) {
            return null;
        }

        $node->name = new Name(self::KEYWORD);
        $firstArgument->value = new String_(trim(substr($firstArgumentValue, strlen(self::KEYWORD))));

        return $node;
    }
}
