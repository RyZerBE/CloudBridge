<?php


namespace BauboLP\Cloud\Packets;


use BauboLP\Cloud\CloudBridge;
use BauboLP\Cloud\Provider\CloudProvider;
use BauboLP\Cloud\Task\TimingDelayPasteTask;
use pocketmine\command\ConsoleCommandSender;

class ServerTimingPacket extends DataPacket
{
    /**
     * @api
     * addData("timing", TIMING_LINK)
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function encode()
    {
        return json_encode($this->data);
    }

    public function handle()
    {
        CloudBridge::getInstance()->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender(), "timings on");
        CloudBridge::getInstance()->getScheduler()->scheduleDelayedTask(new TimingDelayPasteTask(),20*10);
    }
}