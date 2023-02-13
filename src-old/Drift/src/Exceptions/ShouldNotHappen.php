<?php

declare(strict_types=1);

namespace Rector\Pest\Exceptions;

use Exception;
use RuntimeException;
use Throwable;

/** @internal */
final class ShouldNotHappen extends RuntimeException
{
    public function __construct(Throwable $exception)
    {
        $message = $exception->getMessage();

        parent::__construct(sprintf(<<<EOF
This should not happen - please create an new issue here: https://github.com/pestphp/drift.
- Issue: %s
- PHP version: %s
- Operating system: %s
EOF
            , $message, PHP_VERSION, PHP_OS), 1, $exception);
    }

    public static function fromMessage(string $message): self
    {
        return new self(new Exception($message));
    }
}
