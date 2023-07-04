<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;


class WebSocketController extends Controller implements MessageComponentInterface
{
    private $_ids;
    private $clients;

    public function __construct()
    {
        $this->clients = [];
        $this->_ids = [];
    }

    public function init()
    {
        Yii::app()->attachEventHandler('onEndRequest', [$this, 'closeConnection']);
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients[$conn->resourceId] = $conn;
        $msg = json_encode([
            'event' => 'setConnection',
            'connectionId' => $conn->resourceId
        ]);
        $conn->send($msg);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg);
        if ($data->event === 'setId') {
            $this->_ids[$data->_id] = $from->resourceId;
            return;
        }
        if ($data->event === 'message') {
            if (isset($this->_ids[$data->_id]) && isset($this->clients[$this->_ids[$data->_id]])) {
                $this->clients[$this->_ids[$data->_id]]->send(json_encode([
                    'event' => 'online',
                    'message' => $data->message
                ]));
            } else {
                $from->send(json_encode([
                    'event' => 'offline',
                    'message' => 'user is not online'
                ]));
            }
            return;
        }
        if ($data->event === 'broadcast') {
            foreach ($this->clients as $client) {
                if ($from !== $client) {
                    $client->send(json_encode([
                        'message' => $data->message,
                        'test' => json_encode($this->_ids)
                    ]));
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        unset($this->clients[$conn->resourceId]);
        Yii::log("Connection {$conn->resourceId} has disconnected", CLogger::LEVEL_INFO, 'application.controllers.WebSocketController');
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        Yii::log("An error has occurred: {$e->getMessage()}", CLogger::LEVEL_ERROR, 'application.controllers.WebSocketController');
        $conn->close();
    }

    public function closeConnection()
    {
        foreach ($this->clients as $client) {
            $client->close();
        }
        $this->clients = [];
    }

    public function actionIndex()
    {
        require_once dirname(__FILE__) . '/../../vendor/autoload.php';

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new WebSocketController()
                )
            ),
            8900
        );

        $server->run();

        echo "start";
    }
}
