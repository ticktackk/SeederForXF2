<?php

namespace TickTackk\Seeder\Job\Exception;

use Throwable;

class InvalidSeedClassProvidedException extends \InvalidArgumentException
{
    protected $class;

    public function __construct(?string $class, $code = 0, Throwable $previous = null)
    {
        parent::__construct('Invalid seed class provided.', $code, $previous);

        $this->class = $class;
    }

    public function getClass() :? string
    {
        return $this->class;
    }
}