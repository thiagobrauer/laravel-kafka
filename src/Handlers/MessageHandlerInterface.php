<?php

namespace ThiagoBrauer\LaravelKafka\Handlers;

use RdKafka\Message;

interface MessageHandlerInterface
{
    public function handle(string $message);
    public function processKafkaMessage(Message $message);
}
