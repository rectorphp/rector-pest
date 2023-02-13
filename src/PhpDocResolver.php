<?php

declare(strict_types=1);

namespace Rector\Pest;

use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;

final class PhpDocResolver
{
    public function __construct(
        private readonly PhpDocInfoFactory $phpDocInfoFactory
    ) {
    }

    /**
     * @return string[]
     */
    public function resolvePhpDocValuesByName(ClassMethod $classMethod, string $name): array
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($classMethod);
        $targetPhpDocTagNodes = $phpDocInfo->getTagsByName($name);

        return array_map(
            static fn (PhpDocTagNode $phpDocTagNode): string => (string) $phpDocTagNode->value,
            $targetPhpDocTagNodes
        );
    }
}
