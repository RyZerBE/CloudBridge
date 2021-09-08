<?php

namespace BauboLP\Cloud\Packets;

use BauboLP\Cloud\CloudBridge;
use pocketmine\Server;
use function implode;

class ServerInfoPacket extends DataPacket {

    public function handle(){

        $server = Server::getInstance();
        $tps = $server->getTicksPerSecond();
        $tpsPercent = $server->getTickUsage();
        $tpsAverage = $server->getTicksPerSecondAverage();
        $tpsPercentAverage = $server->getTickUsageAverage();
        $players = [];
        foreach($server->getOnlinePlayers() as $player) $players[] = $player->getName();

        $pk = new ServerInfoPacket();
        $pk->addData("tps", $tps);
        $pk->addData("tpsPercent", $tpsPercent);
        $pk->addData("averageTPS", $tpsAverage);
        $pk->addData("averageTPSPercent", $tpsPercentAverage);
        $pk->addData("players", implode(";", $players));
        CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($pk);
    }
}