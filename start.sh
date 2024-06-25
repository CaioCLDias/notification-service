#!/bin/sh

# Função para verificar se o RabbitMQ está disponível
wait_for_rabbitmq() {
  until nc -z -v -w30 rabbitmq 5672
  do
    echo "Waiting for RabbitMQ to start..."
    sleep 5
  done
  echo "RabbitMQ is up and running!"
}

# Chame a função para esperar pelo RabbitMQ
wait_for_rabbitmq

# Inicie o servidor PHP em segundo plano
php -S 0.0.0.0:8000 -t public &

# Execute o comando para escutar a fila RabbitMQ
php artisan rabbitmq:listen
