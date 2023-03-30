<?php

namespace NunoMaduro\Collision\Contracts;

use Whoops\Exception\Frame;

interface CustomEditor
{
    public function getCustomEditorFrame(): Frame|null;
}
