<?php

namespace NunoMaduro\Collision\Adapters\Laravel\Commands;

use Dotenv\Dotenv;
use Dotenv\Repository\RepositoryBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Process;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test {--without-tty : Disable output to TTY}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the application tests';

    /**
     * The arguments to be used while calling phpunit.
     *
     * @var array
     */
    protected $arguments = [
        '--printer',
        'NunoMaduro\Collision\Adapters\Phpunit\Printer',
    ];

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $options = array_slice($_SERVER['argv'], $this->option('without-tty') ? 3 : 2);

        $this->clearEnv();

        $process = (new Process(array_merge(
            $this->binary(),
            array_merge(
                $this->arguments,
                $this->phpunitArguments($options)
            )
        )))->setTimeout(null);

        try {
            $process->setTty(! $this->option('without-tty'));
        } catch (RuntimeException $e) {
            $this->output->writeln('Warning: '.$e->getMessage());
        }

        try {
            return $process->run(function ($type, $line) {
                $this->output->write($line);
            });
        } catch (ProcessSignaledException $e) {
            if (extension_loaded('pcntl') && $e->getSignal() !== SIGINT) {
                throw $e;
            }
        }
    }

    /**
     * Get the PHP binary to execute.
     *
     * @return array
     */
    protected function binary()
    {
        if ('phpdbg' === PHP_SAPI) {
            return [PHP_BINARY, '-qrr', 'vendor/phpunit/phpunit/phpunit'];
        }

        return [PHP_BINARY, 'vendor/phpunit/phpunit/phpunit'];
    }

    /**
     * Get the array of arguments for running PHPUnit.
     *
     * @param  array  $options
     *
     * @return array
     */
    protected function phpunitArguments($options)
    {
        $options = array_values(array_filter($options, function ($option) {
            return ! Str::startsWith($option, '--env=');
        }));

        if (! file_exists($file = base_path('phpunit.xml'))) {
            $file = base_path('phpunit.xml.dist');
        }

        return array_merge(['-c', $file], $options);
    }

    /**
     * Clears any set Environment variables set by Laravel if the --env option is empty.
     *
     * @return void
     */
    protected function clearEnv()
    {
        if (! $this->option('env')) {
            $repositories = RepositoryBuilder::create()
                ->make();

            $envs = Dotenv::create(
                $repositories,
                $this->laravel->environmentPath(),
                $this->laravel->environmentFile()
            )->safeLoad();

            foreach ($envs as $name => $value) {
                $repositories->clear($name);
            }
        }
    }
}
