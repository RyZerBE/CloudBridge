<?php


namespace BauboLP\Cloud\Events;


use BauboLP\Cloud\Packets\DataPacket;
use pocketmine\event\Event;

class CloudPacketSentFailedEvent extends Event
{
    /** @var \BauboLP\Cloud\Packets\DataPacket */
    private $packet;

    public function __construct(DataPacket $dataPacket)
    {
        $this->packet = $dataPacket;
    }

    /**
     * @return \BauboLP\Cloud\Packets\DataPacket
     */
    public function getCloudPacket(): \BauboLP\Cloud\Packets\DataPacket
    {
        return $this->packet;
    }
}