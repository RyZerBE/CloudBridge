<?php


namespace BauboLP\Cloud\Events;


use BauboLP\Cloud\Client\Client;
use BauboLP\Cloud\Packets\DataPacket;
use pocketmine\event\Event;

class CloudPacketSentEvent extends Event
{
    /** @var DataPacket */
    private $packet;
    /** @var Client */
    private $client;

    public function __construct(DataPacket $dataPacket, Client $client)
    {
        $this->packet = $dataPacket;
        $this->client = $client;
    }

    /**
     * @return \BauboLP\Cloud\Packets\DataPacket
     */
    public function getCloudPacket(): \BauboLP\Cloud\Packets\DataPacket
    {
        return $this->packet;
    }

    /**
     * @return \BauboLP\Cloud\Client\Client
     */
    public function getClient(): \BauboLP\Cloud\Client\Client
    {
        return $this->client;
    }
}