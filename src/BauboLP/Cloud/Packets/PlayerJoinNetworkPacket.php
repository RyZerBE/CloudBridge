<?php


namespace BauboLP\Cloud\Packets;


use BauboLP\Cloud\Events\PlayerJoinNetworkEvent;

class PlayerJoinNetworkPacket extends DataPacket
{

    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $ev = new PlayerJoinNetworkEvent($this->data["playerName"]);
        $ev->call();
    }
}