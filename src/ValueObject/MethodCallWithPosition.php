<?php

declare(strict_types=1);

namespace Rector\Pest\ValueObject;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;

final class MethodCallWithPosition
{
    public function __construct(
        private readonly int $position,
        private readonly MethodCall $methodCall
    ) {
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return Arg[]
     */
    public function getArgs(): array
    {
        return $this->methodCall->getArgs();
    }
}
