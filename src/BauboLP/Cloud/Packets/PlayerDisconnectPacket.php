<?php


namespace BauboLP\Cloud\Packets;


class PlayerDisconnectPacket extends DataPacket
{
    /**
     * @api
     * addData("playername", PLAYER_NAME)
     * addData("message", DISCONNECT_REASON)
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