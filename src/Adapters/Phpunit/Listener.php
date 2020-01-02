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

use NunoMaduro\Collision\Contracts\Adapters\Phpunit\Listener as ListenerContract;
use NunoMaduro\Collision\Contracts\Writer as WriterContract;
use NunoMaduro\Collision\Writer;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExceptionWrapper;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use ReflectionObject;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Whoops\Exception\Inspector;

if (class_exists(\PHPUnit\Runner\Version::class) && intval(substr(\PHPUnit\Runner\Version::id(), 0, 1)) >= 7) {

    /**
     * This is an Collision Phpunit Adapter implementation.
     *
     * @author Nuno Maduro <enunomaduro@gmail.com>
     */
    class Listener implements ListenerContract
    {
        /**
         * Holds an instance of the writer.
         *
         * @var \NunoMaduro\Collision\Contracts\Writer
         */
        protected $writer;

        /**
         * Creates a new instance of the class.
         *
         * @param  \NunoMaduro\Collision\Contracts\Writer|null  $writer
         */
        public function __construct(WriterContract $writer = null)
        {
            $this->writer = $writer ?: $this->buildWriter();
        }

        /**
         * {@inheritdoc}
         */
        public function render(\Throwable $t)
        {
            if ($t instanceof ExceptionWrapper && $t->getOriginalException() !== null) {
                $t = $t->getOriginalException();
            }

            $inspector = new Inspector($t);

            $this->writer->getOutput()->writeln('');

            $this->writer->write($inspector);

            if ($t instanceof ExpectationFailedException && $comparisionFailure = $t->getComparisonFailure()) {
                $this->writer->getOutput()->write($comparisionFailure->getDiff());
            }

            $this->terminate();
        }

        /**
         * {@inheritdoc}
         */
        public function addError(Test $test, \Throwable $t, float $time): void
        {
            $this->render($t);
        }

        /**
         * {@inheritdoc}
         */
        public function addWarning(Test $test, Warning $t, float $time): void
        {
        }

        /**
         * {@inheritdoc}
         */
        public function addFailure(Test $test, AssertionFailedError $t, float $time): void
        {
            $this->writer->ignoreFilesIn([
                '/vendor\/phpunit\/phpunit\/src/',
                '/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Testing/'
            ]);

            $reflector = new ReflectionObject($t);

            if ($reflector->hasProperty('message')) {
                $message = trim((string) preg_replace("/\r|\n/", ' ', $t->getMessage()));
                $property = $reflector->getProperty('message');
                $property->setAccessible(true);
                $property->setValue($t, $message);
            }

            $this->render($t);
        }

        /**
         * {@inheritdoc}
         */
        public function addIncompleteTest(Test $test, \Throwable $t, float $time): void
        {
        }

        /**
         * {@inheritdoc}
         */
        public function addRiskyTest(Test $test, \Throwable $t, float $time): void
        {
        }

        /**
         * {@inheritdoc}
         */
        public function addSkippedTest(Test $test, \Throwable $t, float $time): void
        {
        }

        /**
         * {@inheritdoc}
         */
        public function startTestSuite(TestSuite $suite): void
        {
        }

        /**
         * {@inheritdoc}
         */
        public function endTestSuite(TestSuite $suite): void
        {
        }

        /**
         * {@inheritdoc}
         */
        public function startTest(Test $test): void
        {
        }

        /**
         * {@inheritdoc}
         */
        public function endTest(Test $test, float $time): void
        {
        }

        /**
         * Builds an Writer.
         *
         * @return \NunoMaduro\Collision\Contracts\Writer
         */
        protected function buildWriter(): WriterContract
        {
            $writer = new Writer();

            $application = new Application();
            $reflector = new ReflectionObject($application);
            $method = $reflector->getMethod('configureIO');
            $method->setAccessible(true);
            $method->invoke($application, new ArgvInput, $output = new ConsoleOutput);

            return $writer->setOutput($output);
        }

        /**
         * Terminates the test.
         */
        public function terminate(): void
        {
            exit(1);
        }
    }
}
