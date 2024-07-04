<?php

namespace App\Factories;

use App\Consumers\LoginConsumer;
use App\Contracts\ConsumerInterface;

class ConsumerFactory
{
    /**
     * @param string $topic
     * @return ConsumerInterface
     * @throws \Exception
     */
    public function createConsumer(string $topic): ConsumerInterface
    {
        return match ($topic) {
            'login_events' => new LoginConsumer(),
            default => throw new \Exception("Consumer for '{topic}' not found"),
        };
    }
}
