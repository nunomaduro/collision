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

use Exception;
use ReflectionObject;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\Warning;
use Whoops\Exception\Inspector;
use NunoMaduro\Collision\Writer;
use PHPUnit\Framework\TestSuite;
use Symfony\Component\Console\Application;
use PHPUnit\Framework\AssertionFailedError;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use NunoMaduro\Collision\Contracts\Writer as WriterContract;
use NunoMaduro\Collision\Contracts\Adapters\Phpunit\Listener as ListenerContract;

if (class_exists(\PHPUnit\Runner\Version::class) && substr(\PHPUnit\Runner\Version::id(), 0, 2 ) === "6.") {

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
     * Holds the exception found, if any.
     *
     * @var \Throwable|null
     */
    protected $exceptionFound;

    /**
     * Creates a new instance of the class.
     *
     * @param \NunoMaduro\Collision\Contracts\Writer|null $writer
     */
    public function __construct(WriterContract $writer = null)
    {
        $this->writer = $writer ?: $this->buildWriter();
    }

    /**
     * {@inheritdoc}
     */
    public function render(\Throwable $e)
    {
        $inspector = new Inspector($e);

        $this->writer->write($inspector);
    }

    /**
     * {@inheritdoc}
     */
    public function addError(Test $test, Exception $e, $time)
    {
        if ($this->exceptionFound === null) {
            $this->exceptionFound = $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addWarning(Test $test, Warning $e, $time)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function addFailure(Test $test, AssertionFailedError $e, $time)
    {
        $this->writer->ignoreFilesIn(['/vendor/'])
            ->showTrace(false);

        if ($this->exceptionFound === null) {
            $this->exceptionFound = $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addIncompleteTest(Test $test, Exception $e, $time)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function addRiskyTest(Test $test, Exception $e, $time)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function addSkippedTest(Test $test, Exception $e, $time)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function startTestSuite(TestSuite $suite)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function endTestSuite(TestSuite $suite)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function startTest(Test $test)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function endTest(Test $test, $time)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        if ($this->exceptionFound !== null) {
            $this->render($this->exceptionFound);
        }
    }

    /**
     * Builds an Writer.
     *
     * @return \NunoMaduro\Collision\Contracts\Writer
     */
    protected function buildWriter(): WriterContract
    {
        $writer = new Writer;

        $application = new Application();
        $reflector = new ReflectionObject($application);
        $method = $reflector->getMethod('configureIO');
        $method->setAccessible(true);
        $method->invoke($application, new ArgvInput, $output = new ConsoleOutput);

        return $writer->setOutput($output);
    }
}
}