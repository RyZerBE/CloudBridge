<?php


namespace BauboLP\Cloud\Packets;


class CreateServerPacket extends DataPacket
{

    /**
     * @api
     * addData("groupName", GROUP_NAME)
     * addData("count", COUNT_OF_NEEDED_SERVERS)
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