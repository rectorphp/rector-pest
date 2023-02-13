<?php

declare(strict_types=1);

namespace Rector\Pest\NodeFactory;

use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Core\PhpParser\Node\NodeFactory;
use Rector\NodeNameResolver\NodeNameResolver;

final class PestFuncCallFactory
{
    public function __construct(
        private readonly NodeNameResolver $nodeNameResolver,
        private readonly NodeFactory $nodeFactory,
    ) {
    }

    public function create(ClassMethod $classMethod): FuncCall
    {
        $functionName = $this->nodeNameResolver->getName($classMethod);

        $arguments = [
            $functionName,
            new Closure([
                'stmts' => $classMethod->stmts,
                'params' => $classMethod->params,
            ]),
        ];

        return $this->nodeFactory->createFuncCall('test', $arguments);
    }
}
