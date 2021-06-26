<?php


namespace BauboLP\Cloud\Packets;


use BauboLP\Cloud\Events\CloudPacketReceiveEvent;

class Packets
{
    /** @var array  */
    private static $registeredPackets = [];

    /**
     * @param string $packetName
     * @return bool
     */
    public static function isRegistered(string $packetName) {
        return isset(self::$registeredPackets[$packetName]);
    }

    /**
     * @param string $packetName
     * @param string $packet
     */
    public static function registerPacket(string $packetName, string $packet) {
        self::$registeredPackets[$packetName] = $packet;
    }

    /**
     * @param string $packetName
     * @return mixed
     */
    public static function getPacketClassByName(string $packetName): ?DataPacket {
        if(self::isRegistered($packetName)) return new self::$registeredPackets[$packetName];

        return null;
    }

    /**
     * @param string $packetName
     */
    public static function unregisterPacket(string $packetName) {
        unset(self::$registeredPackets[$packetName]);
    }

    /**
     * @param string $packet
     */
    public static function handlePacket(string $packet) {
        $data = json_decode($packet, true);
        if($data['packetName'] == null) return;
        if(!self::isRegistered($data['packetName'])) return;
        $packet = self::getPacketClassByName($data['packetName']);
        if($packet instanceof DataPacket) {
            $packet->data = $data;
            $packet->handle();

            $event = new CloudPacketReceiveEvent($packet);
            $event->call();
        }
    }
}