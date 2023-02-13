<?php

declare(strict_types=1);

namespace Rector\Pest;

use Symfony\Component\Process\Process;

final class RectorRunner
{
    private string $binPath;

    public function __construct(string $binPath)
    {
        $this->binPath = $binPath;
    }

    public function run(string $path, bool $dryRun = false, bool $polish = false): Process
    {
        $rectorConfig = $polish ?
            '/../config/polish-pest.php' :
            '/../config/phpunit-to-pest.php';

        $process = new Process(array_filter([
            $this->binPath,
            'process',
            $path,
            $dryRun ? '--dry-run' : null,
            '--config',
            __DIR__ . $rectorConfig,
            '--ansi',
            '--no-progress-bar',
        ]));

        $process->run();

        return $process;
    }
}
