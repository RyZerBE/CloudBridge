<?php


namespace BauboLP\Cloud\Packets;


class CreatePrivateServerPacket extends DataPacket
{
    /**
     * @api
     * addData("groupName", PLAYER_NAME)
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