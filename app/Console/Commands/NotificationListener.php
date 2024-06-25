<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PharIo\Manifest\Email;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class NotificationListener extends Command
{
    protected $signature = 'rabbitmq:listen';
    protected $description = 'Listen to the RabbitMQ queue for notifications';
    protected  $connection;

    public function __construct(AMQPStreamConnection $connection)
    {
        parent::__construct();
        $this->connection = $connection;
    }

    public function handle()
    {
        $this->info('Connecting to RabbitMQ...');
        $channel = $this->connection->channel();
        $this->info('Connected to RabbitMQ, declaring queue...');

        $channel->queue_declare(env('RABBITMQ_QUEUE'), false, false, false, false);
        $this->info('Queue declared, starting to consume...');

        $callback = function ($msg) {
            $data = json_decode($msg->body, true);
            $this->info('Received message: ' . $msg->body);
            $msg->ack();
        };

        $channel->basic_consume(env('RABBITMQ_QUEUE'), '', false, false, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $this->connection->close();

    }

}
