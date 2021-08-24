<?php


namespace BauboLP\Cloud\Packets;


class DispatchProxyCommandPacket extends DataPacket
{
    /**
     * @api
     * addData("commandLine", COMMAND)
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