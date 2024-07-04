<?php

namespace App\Console\Commands;

use App\Factories\NotificationFactory;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use App\Factories\ConsumerFactory;
use PhpAmqpLib\Message\AMQPMessage;

class NotificationListener extends Command
{
    protected $signature = 'rabbitmq:listen';
    protected $description = 'Listen to the RabbitMQ queue for notifications';
    protected $connection;


    public function __construct(AMQPStreamConnection $connection, )
    {
        parent::__construct();
        $this->connection = $connection;
    }

    public function handle()
    {
        $this->info('Connecting to RabbitMQ...');
        $channel = $this->connection->channel();
        $this->info('Connected to RabbitMQ, declaring queues...');
        $channel->exchange_declare('notification_events', 'direct', false, true, false);

        $queues = config('rabbitmq.queues');

       foreach ($queues as $queue => $routingKey) {
           $channel->queue_declare($queue, false, true, false, false);
           $channel->queue_bind($queue, 'notification_events', $routingKey);
       }

       $this->info('Queues declared and bindings, starting to consume...');

       $callback = function ($msg){
           $data = json_decode($msg->body, true);
           $this->info('Received message: ' . $msg->body);

           $notificationChannel = $data['channel'] ?? 'email';
           $notificationHandler = NotificationFactory::create($notificationChannel);
           $notificationHandler->handle($data);

           $msg->ack();
       };

       foreach ($queues as $queue => $routingKey) {
           $channel->basic_consume($queue, '', false, false, false, false, $callback);
       }

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $this->connection->close();
    }
}
