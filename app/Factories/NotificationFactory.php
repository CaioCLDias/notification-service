<?php

namespace App\Factories;

use App\Services\EmailService;
use App\Services\NotificationInterface;

class NotificationFactory
{

    public static function create(string $channel): NotificationInterface
    {
        return match ($channel) {
            'email' => new EmailService(),
            default => throw new \Exception("Channel '{$channel}' not supported"),
        };
    }

}
