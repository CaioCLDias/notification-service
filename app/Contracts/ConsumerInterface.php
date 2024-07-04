<?php

namespace App\Contracts;

interface ConsumerInterface
{

    public function consume(array $data): void;

}
