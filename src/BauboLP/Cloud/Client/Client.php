<?php


namespace BauboLP\Cloud\Client;


use BauboLP\Cloud\Events\ClientCreateEvent;
use BauboLP\Cloud\Handler\PacketHandler;
use BauboLP\Cloud\Handler\RequestHandler;

class Client
{
    /** @var \BauboLP\Cloud\Handler\RequestHandler */
    private $requestHandler;
    /** @var \BauboLP\Cloud\Handler\PacketHandler */
    private $packetHandler;

    const IP = "5.181.151.61";
    const PORT = 7000;

    public function __construct()
    {
        $this->requestHandler = new RequestHandler(self::IP, self::PORT);
        $this->packetHandler = new PacketHandler();

        $ev = new ClientCreateEvent($this);
        $ev->call();
    }

    /**
     * @return RequestHandler
     */
    public function getRequestHandler(): RequestHandler
    {
        return $this->requestHandler;
    }

    /**
     * @return PacketHandler
     */
    public function getPacketHandler(): PacketHandler
    {
        return $this->packetHandler;
    }
}