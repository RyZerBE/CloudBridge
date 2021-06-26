<?php


namespace BauboLP\Cloud\Packets;


class PlayerDisconnectPacket extends DataPacket
{

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        //Do nothing..
    }
}