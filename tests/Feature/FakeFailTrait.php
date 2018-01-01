<?php

namespace Tests\Feature;

trait FakeFailTrait
{
    public function fakeFail($withDetails = false)
    {
        $args = $withDetails ? '-v' : '';

        return shell_exec("cd ".__DIR__." && php ./FakeProgram/Binary.php $args");
    }
}