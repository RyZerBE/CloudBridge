<?php


namespace BauboLP\Cloud\Packets;


use BauboLP\Cloud\CloudBridge;
use BauboLP\Cloud\Provider\CloudProvider;
use pocketmine\utils\MainLogger;

class ServerShutdownPacket extends DataPacket
{

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        MainLogger::getLogger()->warning(CloudBridge::Prefix."Server wird gestoppt...");
        CloudBridge::getInstance()->getServer()->shutdown();
    }
}