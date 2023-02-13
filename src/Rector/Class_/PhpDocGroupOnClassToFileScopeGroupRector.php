<?php

declare(strict_types=1);

namespace Rector\Pest\Rector\Class_;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Expression;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTagRemover;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see \Rector\Pest\Tests\Rector\Class_\PhpDocGroupOnClassToFileScopeGroupRector\PhpDocGroupOnClassToFileScopeGroupRectorTest
 */
final class PhpDocGroupOnClassToFileScopeGroupRector extends AbstractRector
{
    public function __construct(
        private readonly TestsNodeAnalyzer $testsNodeAnalyzer,
        private readonly PhpDocTagRemover $phpDocTagRemover
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

        $classGroupNames = $this->resolvePhpDocGroupNames($node);
        if ($classGroupNames === []) {
            return null;
        }

        $methodCall = $this->createUsesCall($classGroupNames);

        return [new Expression($methodCall), $node];
    }

    /**
     * @return string[]
     */
    public function resolvePhpDocGroupNames(Class_ $class): array
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($class);

        /** @var PhpDocTagNode[] $groupPhpDocTagNodes */
        $groupPhpDocTagNodes = $phpDocInfo->getTagsByName('group');

        $groupNames = [];

        foreach ($groupPhpDocTagNodes as $groupPhpDocTagNode) {
            if (! $groupPhpDocTagNode->value instanceof GenericTagValueNode) {
                continue;
            }

            $groupNames[] = $groupPhpDocTagNode->value->value;
            $this->phpDocTagRemover->removeTagValueFromNode($phpDocInfo, $groupPhpDocTagNode);
        }

        if ($groupNames === []) {
            return [];
        }

        // invoke original comment position change, to avoid re-printing on different node
        // bug in php-parser printer
        /** @var Class_ $node */
        $node = $class->getAttribute(AttributeKey::ORIGINAL_NODE);
        $node->setAttribute(AttributeKey::COMMENTS, null);
        $class->setAttribute(AttributeKey::ORIGINAL_NODE, null);

        return $groupNames;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Changes @group phpdoc to uses()->group() in Pest', [
            // add code sample
            new CodeSample(
                <<<'CODE_SAMPLE'
use PHPUnit\Framework\TestCase;

/**
 * @group testGroup
 */
class SomeClassTest extends TestCase
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use PHPUnit\Framework\TestCase;

uses()->group('testGroup');

class SomeClassTest extends TestCase
{
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @param string[] $classGroupNames
     */
    private function createUsesCall(array $classGroupNames): MethodCall
    {
        Assert::allString($classGroupNames);
        Assert::notEmpty($classGroupNames);

        $usesCall = new FuncCall(new Name('uses'));

        foreach ($classGroupNames as $classGroupName) {
            $args = [new Arg(new String_($classGroupName))];
            $usesCall = new MethodCall($usesCall, new Identifier('group'), $args);
        }

        return $usesCall;
    }
}
