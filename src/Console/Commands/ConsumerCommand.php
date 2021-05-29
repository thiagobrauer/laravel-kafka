<?php

namespace ThiagoBrauer\LaravelKafka\Console\Commands;

use Illuminate\Console\Command;
use Exception;
use InvalidArgumentException;
use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\Message;
use ThiagoBrauer\LaravelKafka\Handlers\MessageHandler;

class ConsumerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:consume
        {--servers=}
        {--topics=}
        {--group_id=}
        {--commit_async=}
        {--timeout_ms=}
        {--auto_offset_reset=}
        {--auto_commit}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kafka consumer';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $options = $this->getContextOptions();

        $topics = explode(',', $options['topics']);

        $messageHandlers = config('laravel_kafka.message_handlers');

        $this->validateMessageHandlers($messageHandlers);

        $consumer = new KafkaConsumer($this->getConfig());
        $consumer->subscribe($topics);

        while (true) {
            $message = $consumer->consume($options['timeout_ms']);

            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    foreach ($topics as $topic) {
                        if(isset($messageHandlers[$topic])) {
                            foreach ($messageHandlers[$topic] as $messageHandler) {
                                $handler = new $messageHandler();
                                $handler->handle($message);
                            }
                        }
                    }

                    // Commit offsets asynchronously
                    if($options['commit_async'])
                        $consumer->commitAsync($message);
                    else
                        $consumer->commit($message);

                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "No more messages; will wait for more\n";
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo "Timed out\n";
                    break;
                default:
                    throw new Exception($message->errstr(), $message->err);
                    break;
            }
        }
    }

    /**
     * Decode kafka message
     *
     * @param \RdKafka\Message $kafkaMessage
     * @return object
     */
    protected function decodeKafkaMessage(Message $kafkaMessage)
    {
        $message = json_decode($kafkaMessage->payload);

        return $message;
    }

    /**
     * Get kafka config
     *
     * @return \RdKafka\Conf
     */
    protected function getConfig()
    {
        $options = $this->getContextOptions();
        $conf = new Conf();

        // Configure the group.id. All consumer with the same group.id will consume
        // different partitions.
        $conf->set('group.id', $options['group_id']);

        // Initial list of Kafka brokers
        $conf->set('bootstrap.servers', $options['servers']);

        // Set where to start consuming messages when there is no initial offset in
        // offset store or the desired offset is out of range.
        // 'smallest': start from the beginning
        $conf->set('auto.offset.reset', $options['auto_offset_reset']);

        // Automatically and periodically commit offsets in the background
        $conf->set('enable.auto.commit', $options['auto_commit'] ? 'true' : 'false');

        return $conf;
    }

    public function getContextOptions() {
        $options = $this->options();

        if($options['topics'] === null)
            $options['topics'] = config('laravel_kafka.consumer.topics');

        if($options['group_id'] === null)
            $options['group_id'] = config('laravel_kafka.consumer.group_id');

        if($options['commit_async'] === null)
            $options['commit_async'] = config('laravel_kafka.consumer.commit_async');

        if($options['timeout_ms'] === null)
            $options['timeout_ms'] = config('laravel_kafka.consumer.timeout_ms');

        if($options['auto_offset_reset'] === null)
            $options['auto_offset_reset'] = config('laravel_kafka.consumer.auto_offset_reset');

        if($options['auto_commit'] === null)
            $options['auto_commit'] = config('laravel_kafka.consumer.auto_commit');

        if($options['servers'] === null)
            $options['servers'] = config('laravel_kafka.consumer.servers');

        return $options;
    }

    public function validateMessageHandlers($topics) {
        foreach ($topics as $topicMessageHandlers) {
            foreach ($topicMessageHandlers as $messageHandler) {
                if(!is_subclass_of($messageHandler, MessageHandler::class)) {
                    throw new InvalidArgumentException($messageHandler . ' doest not extend '. MessageHandler::class);
                }
            }
        }
    }

}
