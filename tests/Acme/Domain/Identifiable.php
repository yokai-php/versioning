<?php

namespace Yokai\Versioning\Tests\Acme\Domain;

trait Identifiable
{
    public $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
