<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Laravel\Commands;

use Dotenv\Exception\InvalidPathException;
use Dotenv\Parser\Parser;
use Dotenv\Store\StoreBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Env;
use Illuminate\Support\Str;
use NunoMaduro\Collision\Adapters\Laravel\Exceptions\RequirementsException;
use NunoMaduro\Collision\Coverage;
use ParaTest\Options;
use ParaTest\ParaTestCommand;
use RuntimeException;
use SebastianBergmann\Environment\Console;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Process;

/**
 * @internal
 *
 * @final
 */
class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test
        {--without-tty : Disable output to TTY}
        {--compact : Indicates whether the compact printer should be used}
        {--coverage : Indicates whether code coverage information should be collected}
        {--min= : Indicates the minimum threshold enforcement for code coverage}
        {--p|parallel : Indicates if the tests should run in parallel}
        {--profile : Lists top 10 slowest tests}
        {--recreate-databases : Indicates if the test databases should be re-created}
        {--drop-databases : Indicates if the test databases should be dropped}
        {--without-databases : Indicates if database configuration should be performed}
        {--without-cache : Indicates if cache configuration should be performed}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the application tests';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->ignoreValidationErrors();
    }

    /**
     * Run the console command.
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        $this->registerForwardedOptions();

        return parent::run($input, $output);
    }

    /**
     * Declare the options that are only meant to be forwarded to the underlying
     * test runner.
     *
     * Options such as ParaTest's `--processes` are not part of this command's
     * definition. Because the command ignores validation errors, Symfony's
     * parser stops at the first one it does not know and silently drops every
     * token after it, so the command still runs but with the wrong options.
     * Declaring them up front keeps the whole input parsable.
     */
    protected function registerForwardedOptions(): void
    {
        // The application options are merged in as well, so that an option this
        // command does not declare itself, such as `--env`, is not mistaken for
        // one that needs declaring. Options are added to the native definition
        // because `run()` rebuilds the merged one from it.
        $this->mergeApplicationDefinition();

        $definition = $this->getDefinition();
        $nativeDefinition = $this->getNativeDefinition();

        $tokens = $_SERVER['argv'] ?? [];

        if (! is_array($tokens)) {
            return;
        }

        foreach ($tokens as $token) {
            if (! is_string($token) || $token === '--' || ! str_starts_with($token, '--')) {
                continue;
            }

            $name = substr($token, 2);

            if (($position = strpos($name, '=')) !== false) {
                $name = substr($name, 0, $position);
            }

            if ($name === '' || $definition->hasOption($name) || $definition->hasNegation($name)) {
                continue;
            }

            $nativeDefinition->addOption(new InputOption($name, null, InputOption::VALUE_OPTIONAL));
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('coverage') && ! Coverage::isAvailable()) {
            $this->output->writeln(sprintf(
                "\n  <fg=white;bg=red;options=bold> ERROR </> Code coverage driver not available.%s</>",
                Coverage::usingXdebug()
                    ? " Did you set <href=https://xdebug.org/docs/code_coverage#mode>Xdebug's coverage mode</>?"
                    : ' Did you install <href=https://xdebug.org/>Xdebug</> or <href=https://github.com/krakjoe/pcov>PCOV</>?'
            ));

            $this->newLine();

            return 1;
        }

        /** @var bool $usesParallel */
        $usesParallel = $this->option('parallel');

        if ($usesParallel && ! $this->isParallelDependenciesInstalled()) {
            throw new RequirementsException('Running Collision 8.x artisan test command in parallel requires at least ParaTest (brianium/paratest) 7.x.');
        }

        $options = array_slice($_SERVER['argv'], $this->option('without-tty') ? 3 : 2);

        $this->clearEnv();

        $parallel = $this->option('parallel');

        $process = (new Process(array_merge(
            // Binary ...
            $this->binary(),
            // Arguments ...
            $parallel ? $this->paratestArguments($options) : $this->phpunitArguments($options)
        ),
            null,
            // Envs ...
            $parallel ? $this->paratestEnvironmentVariables() : $this->phpunitEnvironmentVariables(),
        ))->setTimeout(null);

        try {
            $process->setTty(! $this->option('without-tty'));
        } catch (RuntimeException $e) {
            // $this->output->writeln('Warning: '.$e->getMessage());
        }

        $exitCode = 1;

        try {
            $exitCode = $process->run(function ($type, $line) {
                $this->output->write($line);
            });
        } catch (ProcessSignaledException $e) {
            if (extension_loaded('pcntl') && $e->getSignal() !== SIGINT) {
                throw $e;
            }
        }

        if ($exitCode === 0 && $this->option('coverage')) {
            if (! $this->usingPest() && $this->option('parallel')) {
                $this->newLine();
            }

            $hideFullCoverage = (bool) $this->option('compact');
            $coverage = Coverage::report($this->output, $hideFullCoverage);

            $exitCode = (int) ($coverage < $this->option('min'));

            if ($exitCode === 1) {
                $this->output->writeln(sprintf(
                    "\n  <fg=white;bg=red;options=bold> FAIL </> Code coverage below expected:<fg=red;options=bold> %s %%</>. Minimum:<fg=white;options=bold> %s %%</>.",
                    number_format($coverage, 1),
                    number_format((float) $this->option('min'), 1)
                ));
            }
        }

        return $exitCode;
    }

    /**
     * Get the PHP binary to execute.
     *
     * @return array
     */
    protected function binary()
    {
        if ($this->usingPest()) {
            $command = $this->option('parallel') ? ['vendor/pestphp/pest/bin/pest', '--parallel'] : ['vendor/pestphp/pest/bin/pest'];
        } else {
            $command = $this->option('parallel') ? ['vendor/brianium/paratest/bin/paratest'] : ['vendor/phpunit/phpunit/phpunit'];
        }

        if ('phpdbg' === PHP_SAPI) {
            return array_merge([PHP_BINARY, '-qrr'], $command);
        }

        return array_merge([PHP_BINARY], $command);
    }

    /**
     * Gets the common arguments of PHPUnit and Pest.
     *
     * @return array
     */
    protected function commonArguments()
    {
        $arguments = [];

        if ($this->option('coverage')) {
            $arguments[] = '--coverage-php';
            $arguments[] = Coverage::getPath();
        }

        if ($this->option('ansi')) {
            $arguments[] = '--colors=always';
        } elseif ($this->option('no-ansi')) {
            $arguments[] = '--colors=never';
        } elseif ((new Console)->hasColorSupport()) {
            $arguments[] = '--colors=always';
        }

        return $arguments;
    }

    /**
     * Determines if Pest is being used.
     *
     * @return bool
     */
    protected function usingPest()
    {
        return function_exists('\Pest\\version');
    }

    /**
     * Get the array of arguments for running PHPUnit.
     *
     * @param  array  $options
     * @return array
     */
    protected function phpunitArguments($options)
    {
        $options = array_merge(['--no-output'], $options);

        $options = array_values(array_filter($options, function ($option) {
            return ! Str::startsWith($option, '--env=')
                && $option != '-q'
                && $option != '--quiet'
                && $option != '--coverage'
                && $option != '--compact'
                && $option != '--profile'
                && $option != '--ansi'
                && $option != '--no-ansi'
                && ! Str::startsWith($option, '--min');
        }));

        return array_merge($this->commonArguments(), ['--configuration='.$this->getConfigurationFile()], $options);
    }

    /**
     * Get the configuration file.
     *
     * @return string
     */
    protected function getConfigurationFile()
    {
        if (! file_exists($file = base_path('phpunit.xml'))) {
            $file = base_path('phpunit.xml.dist');
        }

        return $file;
    }

    /**
     * Get the array of arguments for running Paratest.
     *
     * @param  array  $options
     * @return array
     */
    protected function paratestArguments($options)
    {
        $options = array_values(array_filter($options, function ($option) {
            return ! Str::startsWith($option, '--env=')
                && $option != '--coverage'
                && $option != '-q'
                && $option != '--quiet'
                && $option != '--ansi'
                && $option != '--no-ansi'
                && ! Str::startsWith($option, '--min')
                && ! Str::startsWith($option, '-p')
                && ! Str::startsWith($option, '--compact')
                && ! Str::startsWith($option, '--parallel')
                && ! Str::startsWith($option, '--recreate-databases')
                && ! Str::startsWith($option, '--drop-databases')
                && ! Str::startsWith($option, '--without-databases')
                && ! Str::startsWith($option, '--without-cache');
        }));

        $options = array_merge($this->commonArguments(), [
            '--configuration='.$this->getConfigurationFile(),
            "--runner=\Illuminate\Testing\ParallelRunner",
        ], $options);

        $inputDefinition = new InputDefinition;
        Options::setInputDefinition($inputDefinition);
        $input = new ArgvInput($options, $inputDefinition);

        /** @var non-empty-string $basePath */
        $basePath = base_path();

        $paraTestOptions = Options::fromConsoleInput(
            $input,
            $basePath,
        );

        if (! $paraTestOptions->configuration->hasCoverageCacheDirectory()) {
            $cacheDirectory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'__laravel_test_cache_directory';
            $options[] = '--cache-directory';
            $options[] = $cacheDirectory;
        }

        return $options;
    }

    /**
     * Get the array of environment variables for running PHPUnit.
     *
     * @return array
     */
    protected function phpunitEnvironmentVariables()
    {
        $variables = [
            'COLLISION_PRINTER' => 'DefaultPrinter',
        ];

        if ($this->option('compact')) {
            $variables['COLLISION_PRINTER_COMPACT'] = 'true';
        }

        if ($this->option('profile')) {
            $variables['COLLISION_PRINTER_PROFILE'] = 'true';
        }

        return $variables;
    }

    /**
     * Get the array of environment variables for running Paratest.
     *
     * @return array
     */
    protected function paratestEnvironmentVariables()
    {
        return [
            'LARAVEL_PARALLEL_TESTING' => 1,
            'LARAVEL_PARALLEL_TESTING_RECREATE_DATABASES' => $this->option('recreate-databases'),
            'LARAVEL_PARALLEL_TESTING_DROP_DATABASES' => $this->option('drop-databases'),
            'LARAVEL_PARALLEL_TESTING_WITHOUT_DATABASES' => $this->option('without-databases'),
            'LARAVEL_PARALLEL_TESTING_WITHOUT_CACHE' => $this->option('without-cache'),
        ];
    }

    /**
     * Clears any set Environment variables set by Laravel if the --env option is empty.
     *
     * @return void
     */
    protected function clearEnv()
    {
        if (! $this->option('env')) {
            $vars = self::getEnvironmentVariables(
                $this->laravel->environmentPath(),
                $this->laravel->environmentFile()
            );

            $repository = Env::getRepository();

            foreach ($vars as $name) {
                $repository->clear($name);
            }
        }
    }

    /**
     * @param  string  $path
     * @param  string  $file
     * @return array
     */
    protected static function getEnvironmentVariables($path, $file)
    {
        try {
            $content = StoreBuilder::createWithNoNames()
                ->addPath($path)
                ->addName($file)
                ->make()
                ->read();
        } catch (InvalidPathException $e) {
            return [];
        }

        $vars = [];

        foreach ((new Parser)->parse($content) as $entry) {
            $vars[] = $entry->getName();
        }

        return $vars;
    }

    /**
     * Check if the parallel dependencies are installed.
     *
     * @return bool
     */
    protected function isParallelDependenciesInstalled()
    {
        return class_exists(ParaTestCommand::class);
    }
}
