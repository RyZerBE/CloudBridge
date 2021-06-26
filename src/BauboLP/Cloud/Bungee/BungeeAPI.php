<?php

namespace BauboLP\Cloud\Bungee;

use BauboLP\Cloud\CloudBridge;
use BauboLP\Cloud\Packets\PlayerDisconnectPacket;
use BauboLP\Cloud\Packets\PlayerMessagePacket;
use pocketmine\Player;
use pocketmine\Server;

class BungeeAPI
{

    /**
     * @param string $playerName
     * @param String $server
     * @return void
     * @deprecated
     */
    public static function transferPlayer(string $playerName, String $server)
    {
        CloudBridge::getCloudProvider()->transferPlayer([$playerName], $server);
    }

    /**
     * @param array $players
     * @param String $server
     * @return void
     * @deprecated
     */
    public static function transferPlayers(array $players, String $server)
    {
        CloudBridge::getCloudProvider()->transferPlayer($players, $server);
    }

    /**
     * @param String $player
     * @param String $target
     * @deprecated
     */
    public static function transfer(String $player, String $target)
    {
        self::transferPlayer($player, $target);
    }

    /**
     * @param String $playerName
     * @param String $message
     * @deprecated
     */
    public static function kickPlayer(String $playerName, String $message)
    {
        $pk = new PlayerDisconnectPacket();
        $pk->addData("playerName", $playerName);
        $pk->addData("message", str_replace("§", "&", $message));
        CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($pk);
    }

    /**
     * @param String $message
     * @param String $player
     * @deprecated
     */
    public static function sendMessage(String $message, string $player)
    {
        $pk = new PlayerMessagePacket();
        $pk->addData("message", str_replace("§", "&", $message));
        $pk->addData("players", $player);
        CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($pk);
    }

    public static function getRandomPlayer(): ?Player
    {
        if (count(Server::getInstance()->getOnlinePlayers()) > 0) {
            return Server::getInstance()->getOnlinePlayers()[array_rand(Server::getInstance()->getOnlinePlayers())];
        } else {
            return null;
        }
    }
}
