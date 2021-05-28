<?php

namespace ThiagoBrauer\LaravelKafka\Handlers;

use RdKafka\Message;

interface MessageHandlerInterface
{
    public function handle($message);
    public function processKafkaMessage(Message $message);
}
