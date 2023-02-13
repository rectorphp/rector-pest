<?php

declare(strict_types=1);

namespace Rector\Pest\PHPUnit\ClassMethod;

use Rector\Pest\PestCollector;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\NodeTypeResolver\Node\AttributeKey;

final class AfterClassToAfterAllRector extends AbstractClassMethodRector
{
    public ?string $type = PestCollector::AFTER_ALL;

    public function classMethodRefactor(Class_ $classNode, ClassMethod $classMethodNode): ?FuncCall
    {
        if (! $this->isAfterClassMethod($classMethodNode)) {
            return null;
        }

        return $this->createPestAfterAll($classMethodNode);
    }

    private function isAfterClassMethod(ClassMethod $method): bool
    {
        /** @var PhpDocInfo|null $phpDoc */
        $phpDoc = $method->getAttribute(AttributeKey::PHP_DOC_INFO);

        return $phpDoc && $phpDoc->hasByName('afterClass');
    }

    private function createPestAfterAll(ClassMethod $method): FuncCall
    {
        return $this->builderFactory->funcCall('afterAll', [
            new Closure(['stmts' => $method->stmts]),
        ]);
    }
}
