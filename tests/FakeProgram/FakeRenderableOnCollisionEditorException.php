<?php

declare(strict_types=1);

namespace Tests\FakeProgram;

use Exception;
use NunoMaduro\Collision\Contracts\RenderableOnCollisionEditor;
use Whoops\Exception\Frame;

class FakeRenderableOnCollisionEditorException extends Exception implements RenderableOnCollisionEditor
{
    public function __construct(private string $collisionFile, private int $collisionLine, string $message)
    {
        parent::__construct($message);
    }

    /**
     * {@inheritDoc}
     */
    public function toCollisionEditor(): Frame
    {
        return new Frame([
            'file' => $this->collisionFile,
            'line' => $this->collisionLine,
        ]);
    }
}
