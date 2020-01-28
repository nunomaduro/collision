<?php

/**
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Collision\Adapters\Phpunit;

use NunoMaduro\Collision\Exceptions\ShouldNotHappen;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use PHPUnit\Util\Printer;
use ReflectionObject;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Throwable;

/**
 * This `if` condition exists because phpunit
 * is not a direct dependency of Collision.
 */
if (class_exists(\PHPUnit\Runner\Version::class) && intval(substr(\PHPUnit\Runner\Version::id(), 0, 1)) >= 8) {

    /**
     * This is an Collision Phpunit Adapter implementation.
     *
     * @internal
     */
    final class Listener extends Printer implements TestListener
    {
        /**
         * Holds an instance of the console input.
         *
         * @var InputInterface
         */
        private $input;

        /**
         * Holds an instance of the console input.
         *
         * @var ConsoleOutput
         */
        private $output;

        /**
         * The current section, if any.
         *
         * @var Section
         */
        private $section;

        /**
         * Creates a new instance of the listener.
         *
         * @param  InputInterface  $input
         * @param  ConsoleOutput  $output
         *
         * @throws \ReflectionException
         */
        public function __construct(InputInterface $input = null, ConsoleOutput $output = null)
        {
            parent::__construct();

            $this->input = $input ?? new ArgvInput();
            $this->output = $output ?? new ConsoleOutput();
            ConfigureIO::of($this->input, $this->output);
            $this->section = Section::create($this->output, new TestSuite());
        }

        /**
         * {@inheritdoc}
         */
        public function addError(Test $test, \Throwable $throwable, float $time): void
        {
            $this->section->fail();

            OnError::display($this->output, $throwable);
        }

        /**
         * {@inheritdoc}
         */
        public function addWarning(Test $test, Warning $warning, float $time): void
        {
            $this->section->warn($warning);
        }

        /**
         * {@inheritdoc}
         */
        public function addFailure(Test $test, AssertionFailedError $error, float $time): void
        {
            $this->section->fail();

            $reflector = new ReflectionObject($error);

            if ($reflector->hasProperty('message')) {
                $message = trim((string) preg_replace("/\r|\n/", ' ', $error->getMessage()));
                $property = $reflector->getProperty('message');
                $property->setAccessible(true);
                $property->setValue($error, $message);
            }

            OnError::display($this->output, $error);
        }

        /**
         * {@inheritdoc}
         */
        public function addIncompleteTest(Test $test, \Throwable $t, float $time): void
        {
            $this->section->incomplete($t);
        }

        /**
         * {@inheritdoc}
         */
        public function addRiskyTest(Test $test, \Throwable $t, float $time): void
        {
            $this->section->risky();
        }

        /**
         * {@inheritdoc}
         */
        public function addSkippedTest(Test $test, Throwable $t, float $time): void
        {
            $this->section->skipped($t);
        }

        /**
         * {@inheritdoc}
         */
        public function startTestSuite(TestSuite $suite): void
        {
            $this->section = Section::create($this->output, $suite);
        }

        /**
         * {@inheritdoc}
         */
        public function endTestSuite(TestSuite $suite): void
        {
            $this->section->end();
        }

        /**
         * {@inheritdoc}
         */
        public function startTest(Test $test): void
        {
            if (! $test instanceof TestCase) {
                throw new ShouldNotHappen();
            }

            $this->section->runs($test);
        }

        /**
         * {@inheritdoc}
         */
        public function endTest(Test $test, float $time): void
        {
            $this->section->pass();
        }

        /**
         * Intencionally left blank as we
         * output things on events of the
         * listener.
         *
         * @param  string  $content
         *
         * @return  void
         */
        public function write(string $content): void
        {
            //
        }
    }
}
