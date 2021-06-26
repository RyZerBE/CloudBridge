<?php


namespace BauboLP\Cloud\listener;


use BauboLP\Cloud\Bungee\BungeeAPI;
use BauboLP\Cloud\Provider\CloudProvider;
use baubolp\core\provider\StaffProvider;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class ChatListener implements Listener
{
    /** @var int */
    private $delay;

    public function __construct()
    {
        $this->delay = time();
    }

    public function chat(PlayerChatEvent $event)
    {
        if($this->delay > time()) return; //NO SPAM

        $this->delay = time()+60;
        $serverName = CloudProvider::getServer();
        $server = Server::getInstance();
        $tps = $server->getTicksPerSecond();
        $tpsProcent = $server->getTickUsage();
        $tpsAverage = $server->getTicksPerSecondAverage();
        $tpsProcentAverage = $server->getTickUsageAverage();
        if(stripos($event->getMessage(), "lag") !== false) {
            foreach (StaffProvider::getLoggedStaff() as $staff) {
                BungeeAPI::sendMessage("\n".TextFormat::DARK_GRAY."[".TextFormat::DARK_RED."POSSIBLE LAGG".TextFormat::DARK_GRAY."] ".TextFormat::YELLOW.$serverName.TextFormat::GRAY." soll angeblich laggen:"
                                                   ."\n".TextFormat::GRAY."TPS: ".TextFormat::YELLOW.$tps."($tpsProcent%)"
                                                   ."\n".TextFormat::GRAY."Average TPS: ".TextFormat::YELLOW.$tpsAverage."($tpsProcentAverage%)"
                    , $staff
                );
            }
        }
    }
}