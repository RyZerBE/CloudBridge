<?php


namespace BauboLP\Cloud\Packets;


use BauboLP\Cloud\CloudBridge;
use BauboLP\Cloud\Provider\CloudProvider;

class KeepAlivePacket extends DataPacket
{

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $packet = new KeepAlivePacket();
        CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($packet);
    }
}