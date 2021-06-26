<?php


namespace BauboLP\Cloud\Task;


use BauboLP\Cloud\CloudBridge;
use BauboLP\Cloud\Packets\CreateServerPacket;
use BauboLP\Cloud\Packets\Packets;
use BauboLP\Cloud\Provider\CloudProvider;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class RequestTask extends Task
{
    /** @var bool  */
    private $bool = false;


    public function onRun(int $currentTick)
    {
        if (CloudBridge::getInstance()->getClient() == null) return;
        if (CloudBridge::getInstance()->getClient()->getRequestHandler()->getRestart()) {
            CloudBridge::getInstance()->restartConnection();
            return;
        }

        $queue = CloudBridge::getInstance()->getClient()->getRequestHandler()->requestQueue;
        foreach ($queue as $buffer) {
            Packets::handlePacket($buffer);
            CloudBridge::getInstance()->getClient()->getRequestHandler()->unsetRequest($buffer);
        }

        if (CloudBridge::getCloudProvider()->getMaxPlayersToStartNewServer(explode("-", CloudProvider::getServer())[0]) <= count(Server::getInstance()->getOnlinePlayers())
            && $this->bool == false
            && is_bool(CloudBridge::getCloudProvider()->isServerPrivate(CloudProvider::getServer()))) {
            $this->bool = true;
            $pk = new CreateServerPacket();
            $pk->addData("groupName", explode("-", CloudProvider::getServer())[0]);
            $pk->addData("count", 1);
            CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($pk);
        }
    }
}