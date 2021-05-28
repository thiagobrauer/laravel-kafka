<?php

return [
    'producer' => [
        'servers' => env('KAFKA_PRODUCER_SERVERS', 'kafka:9092'),
        'debug' => env('KAFKA_PRODUCER_DEBUG', true),
        'compression' => env('KAFKA_PRODUCER_COMPRESSION', 'snappy'),
    ],

    'consumer' => [
        'servers' => env('KAFKA_CONSUMER_SERVERS', 'kafka:9092'),
        'topics' => env('KAFKA_CONSUMER_TOPICS', 'inventories'),
        'group_id' => env('KAFKA_CONSUMER_GROUP_ID', 'group1'),
        'auto_offset_reset' => env('KAFKA_CONSUMER_AUTO_OFFSET_RESET', 'earliest'),
        'auto_commit' => env('KAFKA_CONSUMER_AUTO_COMMIT', 'false'),
    ],

    'message_handlers' => [
        //
    ]

];
