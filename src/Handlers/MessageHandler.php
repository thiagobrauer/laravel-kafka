<?php

namespace ThiagoBrauer\LaravelKafka\Handlers;

use RdKafka\Message;

abstract class MessageHandler implements MessageHandlerInterface
{
    public function processKafkaMessage(Message $message)
    {
        return json_decode($message->payload);
    }
}
