<?php


namespace BauboLP\Cloud\Handler;


use BauboLP\Cloud\CloudBridge;
use BauboLP\Cloud\Events\CloudPacketSentEvent;
use BauboLP\Cloud\Events\CloudPacketSentFailedEvent;
use BauboLP\Cloud\Packets\AddPlayerToCloudNotifyPacket;
use BauboLP\Cloud\Packets\ClanWarResultPacket;
use BauboLP\Cloud\Packets\CloudReloadPacket;
use BauboLP\Cloud\Packets\CreatePrivateServerPacket;
use BauboLP\Cloud\Packets\CreateServerPacket;
use BauboLP\Cloud\Packets\DataPacket;
use BauboLP\Cloud\Packets\DispatchProxyCommandPacket;
use BauboLP\Cloud\Packets\InformationPacket;
use BauboLP\Cloud\Packets\KeepAlivePacket;
use BauboLP\Cloud\Packets\MatchPacket;
use BauboLP\Cloud\Packets\NetworkInfoPacket;
use BauboLP\Cloud\Packets\Packets;
use BauboLP\Cloud\Packets\PlayerDisconnectPacket;
use BauboLP\Cloud\Packets\PlayerJoinNetworkPacket;
use BauboLP\Cloud\Packets\PlayerMessagePacket;
use BauboLP\Cloud\Packets\PlayerMoveServerPacket;
use BauboLP\Cloud\Packets\PlayerQuitNetworkPacket;
use BauboLP\Cloud\Packets\RemovePlayerFromCloudNotifyPacket;
use BauboLP\Cloud\Packets\ServerInfoPacket;
use BauboLP\Cloud\Packets\ServerShutdownPacket;
use BauboLP\Cloud\Packets\ServerConnectPacket;
use BauboLP\Cloud\Packets\ServerStatusPacket;
use BauboLP\Cloud\Packets\ServerTimingPacket;
use BauboLP\Cloud\Packets\StartGroupPacket;
use BauboLP\Cloud\Packets\StopGroupPacket;
use BauboLP\Cloud\Packets\StopServerPacket;

use pocketmine\utils\MainLogger;

class PacketHandler
{
    /**
     * @param \BauboLP\Cloud\Packets\DataPacket $packet
     */
    public function writePacket(DataPacket $packet) {
        $client = CloudBridge::getInstance()->getClient();
        if(Packets::isRegistered($packet->getName())) {
            $ev = new CloudPacketSentEvent($packet, $client);
            $ev->call();

            $client->getRequestHandler()->writeData($packet->encode(), $packet);
        }else {
            $ev = new CloudPacketSentFailedEvent($packet);
            $ev->call();
            MainLogger::getLogger()->warning(CloudBridge::Prefix.$packet->getName()." ist KEIN Packet von der Cloud!");
        }
    }

    public function registerPackets(){
        $packets = [
            "InformationPacket" => InformationPacket::class,
            "ServerConnectPacket" => ServerConnectPacket::class,
            "ServerShutdownPacket" => ServerShutdownPacket::class,
            "CreateServerPacket" => CreateServerPacket::class,
            "CloudReloadPacket" => CloudReloadPacket::class,
            "StopGroupPacket" => StopGroupPacket::class,
            "StartGroupPacket" => StartGroupPacket::class,
            "StopServerPacket" => StopServerPacket::class,
            "AddPlayerToCloudNotifyPacket" => AddPlayerToCloudNotifyPacket::class,
            "RemovePlayerFromCloudNotify" => RemovePlayerFromCloudNotifyPacket::class,
            "ServerTimingPacket" => ServerTimingPacket::class,
            "PlayerMoveServerPacket" => PlayerMoveServerPacket::class,
            "ClanWarResultPacket" => ClanWarResultPacket::class,
            "CreatePrivateServerPacket" => CreatePrivateServerPacket::class,
            "DispatchProxyCommandPacket" => DispatchProxyCommandPacket::class,
            "PlayerMessagePacket" => PlayerMessagePacket::class,
            "PlayerDisconnectPacket" => PlayerDisconnectPacket::class,
            "PlayerJoinNetworkPacket" => PlayerJoinNetworkPacket::class,
            "PlayerQuitNetworkPacket" => PlayerQuitNetworkPacket::class,
            "MatchPacket" => MatchPacket::class,
            "ServerInfoPacket" => ServerInfoPacket::class,
            "NetworkInfoPacket" => NetworkInfoPacket::class
        ];

        foreach(array_keys($packets) as $packet)
            Packets::registerPacket($packet, $packets[$packet]);

        MainLogger::getLogger()->info(CloudBridge::Prefix."Es wurden ".count($packets)." Packets registriert");
    }
}