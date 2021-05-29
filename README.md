# Laravel Kafka

This package is based on [anam-hossain's example](https://engineering.carsguide.com.au/laravel-pub-sub-messaging-with-apache-kafka-3b27ed1ee5e8)

## Installation

1. Install the [librdkafka library](https://github.com/edenhill/librdkafka)

2. Install the [php-rdkafka](https://github.com/arnaud-lb/php-rdkafka) PECL extension

4. Install this package using composer:

```bash
composer require thiagobrauer/laravel-kafka
```
5. Publish the package's configuration file:

```bash
php artisan vendor:publish --provider="ThiagoBrauer\LaravelKafka\ServiceProvider"
```
6. Add these properties to your `.env` file, changing the values as needed:
```
KAFKA_PRODUCER_SERVERS=kafka:9092
KAFKA_PRODUCER_DEBUG=true
KAFKA_CONSUMER_SERVERS=kafka:9092
KAFKA_CONSUMER_TOPICS=inventories
KAFKA_CONSUMER_GROUP_ID=group1
```

You can set multiple producer servers, consumer servers and consumer topics, using a `,` as separator.

## Usage

### Producer

To produce a message, just use the `KafkaProducer.php` class:

```php

use ThiagoBrauer\LaravelKafka\KafkaProducer;

...

$producer new KafkaProducer()
$producer->setTopic('topic1')->send('message');
```

### Consumer

First, you need to create a class to handle the messages received. The class must extend `ThiagoBrauer\LaravelKafka\Handlers\MessageHandler` and implement the method `handle`, like the example below:

```php
<?php

namespace App\Kafka\Handlers;

use ThiagoBrauer\LaravelKafka\Handlers\MessageHandler;
use RdKafka\Message;

class KafkaMessageHandler extends MessageHandler
{
    public function handle(Message $message)
    {
        var_dump($message);
    }
}

```
Then, add your class to the `message_handlers` section of your `config/laravel_kafka.php` file, organized by topic:

```php
...

'message_handlers' => [
    'topic1' => [
        App\Kafka\Handlers\KafkaMessageHandler::class   
    ],
    'topic2' => [
        App\Kafka\Handlers\KafkaMessageHandler::class   
    ]        
]

...

```
and run `php artisan config:cache`

After that, you're ready to start the consumer
```bash
php artisan kafka:consume
```

You can define the consumer configuration using variables in your `.env` file or command options:

```
KAFKA_PRODUCER_SERVERS=kafka:9092
KAFKA_PRODUCER_DEBUG=true
KAFKA_PRODUCER_COMPRESSION=snappy
KAFKA_CONSUMER_SERVERS=kafka:9092
KAFKA_CONSUMER_TOPICS=inventories
KAFKA_CONSUMER_GROUP_ID=group1
KAFKA_CONSUMER_COMMIT_ASYNC=true
KAFKA_CONSUMER_TIMEOUT_MS=120000
KAFKA_CONSUMER_AUTO_OFFSET_RESET=earliest
KAFKA_CONSUMER_AUTO_COMMIT=true
```

```sh
php artisan kafka:consume --servers=kafka:9092
php artisan kafka:consume --topics=inventories
php artisan kafka:consume --group_id=group1
php artisan kafka:consume --group_id=group1
php artisan kafka:consume --timeout_ms=120000
php artisan kafka:consume --auto_offset_reset=earliest
php artisan kafka:consume --commit_async

```

