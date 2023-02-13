<?php

declare(strict_types=1);

namespace Rector\Pest\PHPUnit\ClassMethod;

use Rector\Pest\PestCollector;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;

final class SetUpToBeforeEachRector extends AbstractClassMethodRector
{
    public ?string $type = PestCollector::BEFORE_EACH;

    public function classMethodRefactor(Class_ $classNode, ClassMethod $classMethodNode): ?FuncCall
    {
        if (! $this->isSetUpMethod($classMethodNode)) {
            return null;
        }

        return $this->createPestBeforeEach($classMethodNode);
    }

    private function isSetUpMethod(ClassMethod $method): bool
    {
        return $this->isName($method, 'setUp');
    }

    private function createPestBeforeEach(ClassMethod $method): FuncCall
    {
        return $this->builderFactory->funcCall('beforeEach', [
            new Closure(['stmts' => $method->stmts]),
        ]);
    }
}
