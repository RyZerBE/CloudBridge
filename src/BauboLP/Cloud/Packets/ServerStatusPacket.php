<?php


namespace BauboLP\Cloud\Packets;


use BauboLP\Cloud\CloudBridge;
use BauboLP\Cloud\Provider\CloudProvider;
use pocketmine\Server;

class ServerStatusPacket extends DataPacket
{


    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $pk = new ServerStatusPacket();

        $server = Server::getInstance();
        $tps = $server->getTicksPerSecond();
        $tpsProcent = $server->getTickUsage();
        $tpsAverage = $server->getTicksPerSecondAverage();
        $tpsProcentAverage = $server->getTickUsageAverage();

        $pk->addData("tps", $tps);
        $pk->addData("tpsPercent", $tpsProcent);
        $pk->addData("tpsAverage", $tpsAverage);
        $pk->addData("tpsPercentAverage", $tpsProcentAverage);
        CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($pk);
    }
}