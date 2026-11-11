<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Laravel\Inputs;

use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * An argv input that skips the tokens meant for the underlying test runner.
 *
 * The test command forwards options such as `--processes` or `--filter`, and
 * paths, to ParaTest or PHPUnit without declaring them. Symfony's input stops
 * parsing at the first token it does not know, so any of the command's own
 * options after it would be silently ignored. This input skips such tokens
 * and keeps parsing instead.
 *
 * @internal
 */
final class ForwardedArgvInput extends ArgvInput
{
    /**
     * Creates a new input from the tokens and state of the given input.
     */
    public static function fromArgvInput(ArgvInput $input): self
    {
        $forwarded = new self(['', ...$input->getRawTokens()]);

        $forwarded->interactive = $input->interactive;
        $forwarded->stream = $input->stream;

        return $forwarded;
    }

    /**
     * {@inheritdoc}
     */
    protected function parseToken(string $token, bool $parseOptions): bool
    {
        try {
            return parent::parseToken($token, $parseOptions);
        } catch (RuntimeException) {
            return $parseOptions;
        }
    }
}
