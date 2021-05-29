<?php

namespace ThiagoBrauer\LaravelKafka\Handlers;

use RdKafka\Message;

interface MessageHandlerInterface
{
    public function handle(Message $message);
}
