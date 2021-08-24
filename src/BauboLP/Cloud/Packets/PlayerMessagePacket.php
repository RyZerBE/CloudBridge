<?php


namespace BauboLP\Cloud\Packets;


class PlayerMessagePacket extends DataPacket
{
    /**
     * @api
     * addData("players", PLAYER_NAMES - "BauboLPYT:Matze998:Chillihero")
     * addData("message", MESSAGE - "&cBIG &ePARTY")
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
       //Do nothing..
    }
}