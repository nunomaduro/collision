<?php

declare(strict_types=1);

namespace Tests\Unit\Adapters;

use NunoMaduro\Collision\Adapters\Laravel\Inputs\ForwardedArgvInput;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class ForwardedArgvInputTest extends TestCase
{
    /**
     * @return array<string, array{0: list<string>}>
     */
    public static function tokensMeantForTheTestRunner(): array
    {
        return [
            'long option with inline value' => [['--processes=3']],
            'long option with separate value' => [['--processes', '3']],
            'long option without value' => [['--stop-on-failure']],
            'short option with separate value' => [['-d', 'memory_limit=1G']],
            'short option with inline value' => [['-dmemory_limit=1G']],
            'path argument' => [['tests/Unit']],
        ];
    }

    /**
     * @param  list<string>  $tokens
     */
    #[Test]
    #[DataProvider('tokensMeantForTheTestRunner')]
    public function options_after_tokens_meant_for_the_test_runner_are_parsed(array $tokens): void
    {
        $input = new ForwardedArgvInput(['artisan', 'test', ...$tokens, '--parallel', '--min=80', '--recreate-databases']);

        $input->bind($this->definition());

        $this->assertSame('test', $input->getArgument('command'));
        $this->assertTrue($input->getOption('parallel'));
        $this->assertSame('80', $input->getOption('min'));
        $this->assertTrue($input->getOption('recreate-databases'));
    }

    #[Test]
    public function options_before_tokens_meant_for_the_test_runner_are_parsed(): void
    {
        $input = new ForwardedArgvInput(['artisan', 'test', '-p', '--min', '80', '--processes=3', 'tests/Unit']);

        $input->bind($this->definition());

        $this->assertTrue($input->getOption('parallel'));
        $this->assertSame('80', $input->getOption('min'));
        $this->assertFalse($input->getOption('recreate-databases'));
    }

    #[Test]
    public function it_keeps_the_raw_tokens_and_input_state(): void
    {
        $original = new ArgvInput(['artisan', 'test', '--processes=3', '--parallel']);
        $original->setInteractive(false);
        $original->setStream($stream = fopen('php://memory', 'r'));

        $input = ForwardedArgvInput::fromArgvInput($original);

        $this->assertSame(['test', '--processes=3', '--parallel'], $input->getRawTokens());
        $this->assertFalse($input->isInteractive());
        $this->assertSame($stream, $input->getStream());
    }

    private function definition(): InputDefinition
    {
        return new InputDefinition([
            new InputArgument('command', InputArgument::REQUIRED),
            new InputOption('parallel', 'p', InputOption::VALUE_NONE),
            new InputOption('min', null, InputOption::VALUE_REQUIRED),
            new InputOption('recreate-databases', null, InputOption::VALUE_NONE),
        ]);
    }
}
