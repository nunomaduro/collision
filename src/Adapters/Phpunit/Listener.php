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
use PHPUnit\Framework\TestSuite;
use NunoMaduro\Collision\Writer;
use PHPUnit\Framework\TestListener;
use Symfony\Component\Console\Application;
use PHPUnit\Framework\AssertionFailedError;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use NunoMaduro\Collision\Contracts\Writer as WriterContract;

/**
 * This is an Collision Phpunit Adapter implementation.
 *
 * @author Nuno Maduro <enunomaduro@gmail.com>
 */
class Listener implements TestListener
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
     * @param \NunoMaduro\Collision\Contracts\Writer|null $writer
     */
    public function __construct(WriterContract $writer = null)
    {
        $this->writer = $writer ?: $this->buildWriter();
    }

    /**
     * {@inheritdoc}
     */
    public function addError(Test $test, Exception $e, $time)
    {
        $inspector = new Inspector($e);

        $this->writer->write($inspector);

        exit();
    }

    /**
     * {@inheritdoc}
     */
    public function addWarning(Test $test, Warning $e, $time)
    {
        $this->addError($test, $e, $time);
    }

    /**
     * {@inheritdoc}
     */
    public function addFailure(Test $test, AssertionFailedError $e, $time)
    {
        $this->writer->ignoreFilesIn(['/vendor/'])
            ->showTrace(false);

        $this->addError($test, $e, $time);
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
