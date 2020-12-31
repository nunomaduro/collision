<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Laravel\Exceptions;

use RuntimeException;
use NunoMaduro\Collision\Contracts\RenderlessEditor;
use NunoMaduro\Collision\Contracts\RenderlessTrace;

/**
 * @internal
 */
final class RequirementsException extends RuntimeException implements RenderlessEditor, RenderlessTrace
{
}
