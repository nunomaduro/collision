<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CommandThatValidates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command-that-validates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will throw a validation exception';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        Validator::make(['foo' => 'bar', 'baz' => null], [
            'foo' => ['integer', 'numeric'],
            'baz' => ['required', 'string']
        ])->validate();

        return Command::FAILURE;
    }
}
