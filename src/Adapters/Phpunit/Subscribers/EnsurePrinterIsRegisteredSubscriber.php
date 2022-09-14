<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit\Subscribers;

use NunoMaduro\Collision\Adapters\Phpunit\Printers\DefaultPrinter;
use PHPUnit\Event\Facade;
use PHPUnit\Event\TestRunner\Configured;
use PHPUnit\Event\TestRunner\ConfiguredSubscriber;

/**
 * @internal
 */
final class EnsurePrinterIsRegisteredSubscriber implements ConfiguredSubscriber
{
    /**
     * If this subscriber has been registered on PHPUnit's facade.
     */
    private static bool $registered = false;

    /**
     * Runs the subscriber.
     */
    public function notify(Configured $event): void
    {
        $configuration = $event->configuration();

        $printerClass = \sprintf(
            '\NunoMaduro\Collision\Adapters\Phpunit\Printers\%s',
            $_SERVER['COLLISION_PRINTER']
        );

        if (class_exists($printerClass)) {
            /** @var DefaultPrinter $printer */
            $printer = new $printerClass($configuration->colors());

            Facade::registerSubscribers(
                new BeforeTestClassMethodErroredSubscriber($printer),
                new TestConsideredRiskySubscriber($printer),
                new TestTriggeredPhpDeprecationSubscriber($printer),
                new TestErroredSubscriber($printer),
                new TestFailedSubscriber($printer),
                new TestFinishedSubscriber($printer),
                new TestMarkedIncompleteSubscriber($printer),
                new TestPreparedSubscriber($printer),
                new TestRunnerExecutionStartedSubscriber($printer),
                new TestSkippedSubscriber($printer),
                new TestRunnerExecutionFinishedSubscriber($printer),
            );
        }
    }

    /**
     * Registers the subscriber on PHPUnit's facade.
     */
    public static function register(): void
    {
        $shouldRegister = self::$registered === false
            && isset($_SERVER['COLLISION_PRINTER']);

        if ($shouldRegister) {
            Facade::registerSubscriber(new self());
        }
    }
}
