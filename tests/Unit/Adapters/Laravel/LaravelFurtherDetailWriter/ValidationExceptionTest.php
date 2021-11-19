<?php

namespace Tests\Unit\Adapters\Laravel\LaravelFurtherDetailWriter;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use NunoMaduro\Collision\Adapters\Laravel\LaravelFurtherDetailWriter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use Mockery as m;

class ValidationExceptionTest extends TestCase
{

    /** @test */
    public function itOutputsTheValidationErrorsCorrectly()
    {
        $exception = $this->getException([
            'foo' => ['Something went wrong.'],
            'bar' => ['This is required.', 'It should be an integer.']
        ]);

        $output = new BufferedOutput();
        $writer = new LaravelFurtherDetailWriter();

        $writer->write($output, $exception);

        $result = <<<EOF

  The exception contains the following validation errors:

  [
    'foo' => [
      'Something went wrong.',
    ],
    'bar' => [
      'This is required.',
      'It should be an integer.',
    ]
  ];


EOF;

        $this->assertEquals($output->fetch(), $result);
    }

    private function getException(array $messages): ValidationException
    {
        $validator = m::mock(Validator::class);
        $validator->shouldReceive('errors')
            ->andReturn(new MessageBag($messages));

        return new ValidationException($validator);
    }

}
