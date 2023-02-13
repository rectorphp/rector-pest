<?php

declare(strict_types=1);

namespace Rector\Pest\Rector\FuncCall;

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
    /**
     * @var string
     */
    private const KEYWORD = 'it';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Renames tests starting with `it` to use the `it()` function', [
            new CodeSample(
                <<<'PHP'
test('it starts with it')->skip();
PHP
                ,
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

        $args = $node->getArgs();
        if ($args === []) {
            return null;
        }

        if (! $args[0]->value instanceof String_) {
            return null;
        }

        $string = $args[0]->value;
        if (! $string instanceof String_) {
            return null;
        }

        if (! \str_starts_with($string->value, self::KEYWORD)) {
            return null;
        }

        $node->name = new Name(self::KEYWORD);

        $string->value = trim(substr($string->value, strlen(self::KEYWORD)));

        return $node;
    }
}
