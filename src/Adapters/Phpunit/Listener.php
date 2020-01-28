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
         * Holds an instance of Section.
         *
         * @var Section
         */
        private $section;

        /**
         * The timer.
         *
         * @var Timer
         */
        private $timer;

        /**
         * The number of total tests.
         *
         * @var int|null
         */
        private $totalTests;

        /**
         * Indicates that the method `end`
         * was called already.
         *
         * @var bool
         */
        private $sectionEnded = false;

        /**
         * The current test number.
         *
         * @var int
         */
        private $currentTestNumber = 0;

        /**
         * The number of passed tests.
         *
         * @var int
         */
        private $passedTests = 0;

        /**
         * The number of skipped tests.
         *
         * @var int
         */
        private $skippedTests = 0;

        /**
         * The number of warning tests.
         *
         * @var int
         */
        private $warningsTests = 0;

        /**
         * The number of incomplete tests.
         *
         * @var int
         */
        private $incompleteTests = 0;

        /**
         * The number of risky tests.
         *
         * @var int
         */
        private $riskyTests = 0;

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
            $this->section = new Section($this->output);

            /**
             * Starts the timer.
             */
            $this->timer = Timer::start();
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
            $this->warningsTests++;

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
            $this->incompleteTests++;
            $this->section->incomplete($t);
        }

        /**
         * {@inheritdoc}
         */
        public function addRiskyTest(Test $test, \Throwable $t, float $time): void
        {
            $this->riskyTests++;
            $this->section->risky();
        }

        /**
         * {@inheritdoc}
         */
        public function addSkippedTest(Test $test, Throwable $t, float $time): void
        {
            $this->skippedTests++;
            $this->section->skipped($t);
        }

        /**
         * {@inheritdoc}
         */
        public function startTestSuite(TestSuite $suite): void
        {
            if ($this->totalTests === null) {
                $this->totalTests = $suite->count();
            }
        }

        /**
         * {@inheritdoc}
         */
        public function endTestSuite(TestSuite $suite): void
        {
            if (! $this->sectionEnded && $this->totalTests === $this->currentTestNumber) {
                $this->sectionEnded = true;
                $this->section->end();
                $this->output->writeln('');

                $tests = [];

                foreach (['warnings', 'risky', 'incomplete', 'skipped'] as $countName) {
                    if ($countTests = $this->{$countName . 'Tests'}) {
                        $tests[] = "<fg=yellow;options=bold>$countTests $countName</>";
                    }
                }

                if ($passedTests = $this->passedTests) {
                    $tests[] = "<fg=green;options=bold>$passedTests passed</>";
                }

                $totalTests = $this->totalTests;
                $tests[] = "$totalTests total";

                $this->output->writeln(
                    sprintf(
                        '  <fg=white;options=bold>Tests:  </><fg=default>%s</>',
                        implode(', ', $tests)
                    )
                );

                $timeElapsed = number_format($this->timer->result(), 2, '.', '');
                $this->output->writeln(
                    sprintf(
                        '  <fg=white;options=bold>Time:   </><fg=default>%ss</>',
                        $timeElapsed
                    )
                );
            }
        }

        /**
         * {@inheritdoc}
         */
        public function startTest(Test $test): void
        {
            if (! $test instanceof TestCase) {
                throw new ShouldNotHappen();
            }

            $this->currentTestNumber++;

            $this->section->runs($test);
        }

        /**
         * {@inheritdoc}
         */
        public function endTest(Test $test, float $time): void
        {
            if ($this->section->shouldPass) {
                $this->passedTests++;
                $this->section->pass();
            }
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
