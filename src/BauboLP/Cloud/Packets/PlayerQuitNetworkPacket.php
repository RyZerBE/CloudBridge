<?php


namespace BauboLP\Cloud\Packets;


use BauboLP\Cloud\Events\PlayerQuitNetworkEvent;

class PlayerQuitNetworkPacket extends DataPacket
{

    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $ev = new PlayerQuitNetworkEvent($this->data["playerName"]);
        $ev->call();
    }
}