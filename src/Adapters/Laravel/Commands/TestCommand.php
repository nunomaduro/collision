<?php

namespace NunoMaduro\Collision\Adapters\Laravel\Commands;

use Illuminate\Console\Command;
use NunoMaduro\Collision\Adapters\Phpunit\TestRunner;

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
        $withoutTty = $this->option('without-tty');

        $options = \array_slice($_SERVER['argv'], $withoutTty ? 3 : 2);

        $runner = new TestRunner(base_path(), (bool) $withoutTty);

        return $runner($this->output, $options);
    }
}
