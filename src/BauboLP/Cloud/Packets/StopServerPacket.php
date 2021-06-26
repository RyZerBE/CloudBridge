<?php


namespace BauboLP\Cloud\Packets;


class StopServerPacket extends DataPacket
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