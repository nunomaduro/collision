<?php

declare(strict_types=1);

namespace Tests\FakeProgram;

class HelloWorldFile5
{
    public static function say(): FakeRenderableOnCollisionEditorException
    {
        return new FakeRenderableOnCollisionEditorException(
            __DIR__.'/FakeRenderableOnCollisionEditorException.php',
            16, 'Fail custom editor description'
        );
    }
}
