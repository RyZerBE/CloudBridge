<?php


namespace BauboLP\Cloud\Packets;


class StopServerPacket extends DataPacket
{
    /**
     * @api
     * addData("serverName", GROUP_NAME)
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