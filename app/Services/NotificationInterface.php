<?php

namespace App\Services;

interface NotificationInterface
{
    public function handle(array $data): void;
}
