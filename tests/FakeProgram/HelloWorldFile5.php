<?php

declare(strict_types=1);

namespace Tests\FakeProgram;

class HelloWorldFile5
{
    public static function say(): FakeCustomEditorException
    {
        return (new FakeCustomEditorException('Fail custom editor description'))
            ->withCustomEditor(__DIR__.'/FakeCustomEditorException.php', 15);
    }
}
