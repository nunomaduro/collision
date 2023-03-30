<?php

declare(strict_types=1);

namespace Tests\FakeProgram;

use Exception;
use NunoMaduro\Collision\Concerns\RendersCustomEditor;
use NunoMaduro\Collision\Contracts\CustomEditor;

class FakeCustomEditorException extends Exception implements CustomEditor
{
    use RendersCustomEditor;

    public static function make(string $message): FakeCustomEditorException
    {
        return new self($message);
    }
}
