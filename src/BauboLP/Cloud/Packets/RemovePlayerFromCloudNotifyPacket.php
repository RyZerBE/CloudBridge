<?php


namespace BauboLP\Cloud\Packets;


class RemovePlayerFromCloudNotifyPacket extends DataPacket
{
    /**
     * @api
     * addData("playerName", PLAYER_NAME)
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