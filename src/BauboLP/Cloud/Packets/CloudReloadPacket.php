<?php


namespace BauboLP\Cloud\Packets;


use BauboLP\Cloud\CloudBridge;
use BauboLP\Cloud\Provider\CloudProvider;

class CloudReloadPacket extends DataPacket
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