<?php

namespace NunoMaduro\Collision\Concerns;

use Whoops\Exception\Frame;

trait RendersCustomEditor
{
    private string $customEditorFile;

    private int $customEditorLine;

    public function getCustomEditorFrame(): Frame|null
    {
        if (! isset($this->customEditorFile)) {
            return null;
        }

        return new Frame([
            'file' => $this->customEditorFile,
            'line' => $this->customEditorLine,
        ]);
    }

    public function withCustomEditor(string $file, int $line): static
    {
        $this->customEditorFile = $file;
        $this->customEditorLine = $line;

        return $this;
    }
}
