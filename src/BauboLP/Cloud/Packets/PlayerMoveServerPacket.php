<?php


namespace BauboLP\Cloud\Packets;


class PlayerMoveServerPacket extends DataPacket
{
    /**
     * @api
     * addData("playerNames", PLAYER_NAMES - "BauboLPYT:Matze998:Chillihero")
     * addData("serverName", SERVERNAME)
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        parent::handle();
    }
}