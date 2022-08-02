<?php

namespace App\Http\SocketHandler;

use BeyondCode\LaravelWebSockets\WebSockets\Messages\PusherChannelProtocolMessage;
use BeyondCode\LaravelWebSockets\WebSockets\WebSocketHandler;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;

class MyCustomHandler extends PusherChannelProtocolMessage implements MessageComponentInterface
{

    public function onOpen(ConnectionInterface $connection)
    {
        // TODO: Implement onOpen() method.
        return $connection;
    }

    public function onClose(ConnectionInterface $connection)
    {
        // TODO: Implement onClose() method.
    }

    public function onError(ConnectionInterface $connection, \Exception $e)
    {
        // TODO: Implement onError() method.
    }

//    protected function ping(ConnectionInterface $connection)
//    {
//        parent::ping($connection); // TODO: Change the autogenerated stub
//        $data = 'data_ekav_ping';
//
//        $connection->send(json_encode([
//            'event' => 'pusher:pong',
//            'data' => $data
//        ]));
//    }

    public function onMessage(ConnectionInterface $connection, MessageInterface $msg)
    {
        // TODO: Implement onMessage() method.
//        parent::onMessage($connection, $msg);
//        return $msg . 'aaaaa';
    }
}