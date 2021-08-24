<?php


namespace BauboLP\Cloud\Packets;


class StopGroupPacket extends DataPacket
{
    /**
     * @api
     * addData("groupName", GROUP_NAME)
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