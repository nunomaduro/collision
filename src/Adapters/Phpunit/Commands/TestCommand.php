<?php

/**
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Collision\Adapters\Phpunit\Commands;

use NunoMaduro\Collision\Adapters\Phpunit\TestRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TestCommand extends Command
{
    /**
     * Test working path.
     *
     * @var string
     */
    protected $workingPath;

    /**
     * Create a new command instance.
     *
     * @param  string  $workingPath
     */
    public function __construct(string $workingPath)
    {
        $this->workingPath = $workingPath;

        parent::__construct();
    }

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this->setName('run')
            ->setDescription('Run the tests.')
            ->addOption('without-tty', null, InputOption::VALUE_NONE, 'Disable output to TTY.');
    }

    /**
     * Execute the command.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     *
     * @return int 0 if everything went fine, or an exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $withoutTty = $input->getOption('without-tty');

        $options = array_slice($_SERVER['argv'], $withoutTty ? 3 : 2);

        $runner = new TestRunner($this->workingPath, (bool) $withoutTty);

        return $runner(new SymfonyStyle($input, $output), $options);
    }
}
