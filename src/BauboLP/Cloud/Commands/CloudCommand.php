<?php


namespace BauboLP\Cloud\Commands;


use BauboLP\Cloud\CloudBridge;
use BauboLP\Cloud\Packets\AddPlayerToCloudNotifyPacket;
use BauboLP\Cloud\Packets\CloudReloadPacket;
use BauboLP\Cloud\Packets\CreateServerPacket;
use BauboLP\Cloud\Packets\PlayerMoveServerPacket;
use BauboLP\Cloud\Packets\RemovePlayerFromCloudNotifyPacket;
use BauboLP\Cloud\Packets\StartGroupPacket;
use BauboLP\Cloud\Packets\StopGroupPacket;
use BauboLP\Cloud\Packets\StopServerPacket;
use BauboLP\Cloud\Provider\CloudProvider;
use BauboLP\Cloud\Task\TimingDelayPasteTask;
use iTzFreeHD\CloudSystem\CloudSystem;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class CloudCommand extends Command
{


    public function __construct()
    {
        parent::__construct('cloud', "CloudBridge", "", ['']);
        $this->setPermission("cloud.admin");
        $this->setPermissionMessage(CloudBridge::Prefix . TextFormat::RED . "No Permissions!");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$this->testPermission($sender)) return;

        if (empty($args[0])) {
            $sender->sendMessage(CloudBridge::Prefix . TextFormat::RED . "/cloud help");
            return;
        }

        if (empty($args[1])) {
            if ($args[0] == "reload") {
                $packet = new CloudReloadPacket();
                CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($packet);
                $sender->sendMessage(CloudBridge::Prefix . "Befehl zur Cloud gesendet.");
                return;
            }else if($args[0] == "timing") {
                CloudBridge::getInstance()->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender(), "timings on");
                CloudBridge::getInstance()->getScheduler()->scheduleDelayedTask(new TimingDelayPasteTask(),20*10);
                $sender->sendMessage(CloudBridge::Prefix."Dein Timing wird erstellt.. Sobald das Timing aufrufbar ist, wird es zur Cloud gesendet.");
                return;
            }else if($args[0] == "listservers") {
                $servers = CloudBridge::Prefix.TextFormat::YELLOW."Laufende CloudServer:\n";
                foreach (CloudBridge::getCloudProvider()->getRunningServers() as $server) {
                    if($server != "blacklist" && $server != "." && $server != "..")
                        $servers .= TextFormat::DARK_GRAY."» ".TextFormat::AQUA.$server."\n";
                }
                $sender->sendMessage($servers);
                return;
            }else if($args[0] == "listgroups") {
                $servers = CloudBridge::Prefix.TextFormat::YELLOW."CloudGroups:\n";
                foreach (CloudBridge::getCloudProvider()->getGroups() as $group)
                    $servers .= TextFormat::DARK_GRAY."» ".TextFormat::AQUA.$group."\n";

                $sender->sendMessage($servers);
                return;
            }
            if ($args[0] == "help") {
                $helpList =
                    CloudBridge::Prefix . "/cloud reload\n" .
                    CloudBridge::Prefix . "/cloud timing\n" .
                    CloudBridge::Prefix . "/cloud startserver <Group> <Count>\n" .
                    CloudBridge::Prefix . "/cloud stopserver <Server>\n" .
                    CloudBridge::Prefix . "/cloud startgroup <Group>\n" .
                    CloudBridge::Prefix . "/cloud stopgroup <Group>\n" .
                    CloudBridge::Prefix . "/cloud send <Players> <Server>\n" .
                    CloudBridge::Prefix . "/cloud listservers\n" .
                    CloudBridge::Prefix . "/cloud listgroups\n" .
                    CloudBridge::Prefix . "/cloud notify <login|logout>\n";
                $sender->sendMessage($helpList);
                return;
            }
            $sender->sendMessage(CloudBridge::Prefix . TextFormat::RED . "/cloud help");
            return;
        }

        switch ($args[0]) {
            case "startserver":
                if (isset($args[2])) {
                    if (!is_numeric($args[2])) {
                        $sender->sendMessage(CloudBridge::Prefix . "Bitte gebe eine Zahl als Count an!");
                        return;
                    }
                    if (!is_string($args[1])) {
                        $sender->sendMessage(CloudBridge::Prefix . "Bitte gebe eine richtige Gruppe an!");
                        return;
                    }
                    $pk = new CreateServerPacket();
                    $pk->addData("groupName", $args[1]);
                    $pk->addData("count", $args[2]);
                    CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($pk);
                    $sender->sendMessage(CloudBridge::Prefix . "Befehl zur Cloud gesendet.");
                    return;
                }
                break;
            case "stopserver":
                if(!is_string($args[1])) {
                    $sender->sendMessage(CloudBridge::Prefix . "Bitte gebe einen Server an!");
                    return;
                }
                $pk = new StopServerPacket();
                $pk->addData("serverName", $args[1]);
                CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($pk);
                $sender->sendMessage(CloudBridge::Prefix . "Befehl zur Cloud gesendet.");
                break;
            case "startgroup":
                if(!is_string($args[1])) {
                    $sender->sendMessage(CloudBridge::Prefix . "Bitte gebe eine Gruppe an!");
                    return;
                }
                $pk = new StartGroupPacket();
                $pk->addData("groupName", $args[1]);
                CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($pk);
                $sender->sendMessage(CloudBridge::Prefix . "Befehl zur Cloud gesendet.");
                break;
            case "stopgroup":
                if(!is_string($args[1])) {
                    $sender->sendMessage(CloudBridge::Prefix . "Bitte gebe eine Gruppe an!");
                    return;
                }
                $pk = new StopGroupPacket();
                $pk->addData("groupName", $args[1]);
                CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($pk);
                $sender->sendMessage(CloudBridge::Prefix . "Befehl zur Cloud gesendet.");
                break;
            case "notify":
                if(!$sender instanceof Player) return;
                if($args[1] == "login") {
                    $packet = new AddPlayerToCloudNotifyPacket();
                    $packet->addData("playerName", $sender->getName());
                    CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($packet);
                    $sender->sendMessage(CloudBridge::Prefix . "Befehl zur Cloud gesendet.");
                } else if($args[1] == "logout") {
                    $packet = new RemovePlayerFromCloudNotifyPacket();
                    $packet->addData("playerName", $sender->getName());
                    CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($packet);
                    $sender->sendMessage(CloudBridge::Prefix . "Befehl zur Cloud gesendet.");
                }else {
                    $sender->sendMessage(CloudBridge::Prefix."/cloud notify <login|logout>");
                }
                break;
            case "send":
                if(!$sender instanceof Player) return;
                if(empty($args[2])) {
                    $sender->sendMessage(CloudBridge::Prefix.TextFormat::RED."/cloud send <Players> <Server>");
                    $sender->sendMessage(CloudBridge::Prefix.TextFormat::RED."Example for one player: /cloud send BauboLPYT <Server>");
                    $sender->sendMessage(CloudBridge::Prefix.TextFormat::RED."Example for more than 1 player: /cloud send BauboLPYT:Chillihero <Server>");
                    return;
                }

                if(!in_array($args[2], CloudBridge::getCloudProvider()->getRunningServers())) {
                    $sender->sendMessage(CloudBridge::Prefix.TextFormat::RED."Dieser Server existiert nicht!");
                    return;
                }
                $pk = new PlayerMoveServerPacket();
                $pk->addData("serverName", $args[2]);
                $pk->addData("playerNames", $args[1]);
                CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($pk);
                $sender->sendMessage(CloudBridge::Prefix . "Befehl zur Cloud gesendet.");
                break;
            default:
                $helpList =
                    CloudBridge::Prefix . "/cloud reload\n" .
                    CloudBridge::Prefix . "/cloud timing\n" .
                    CloudBridge::Prefix . "/cloud startserver <Group> <Count>\n" .
                    CloudBridge::Prefix . "/cloud stopserver <Server>\n" .
                    CloudBridge::Prefix . "/cloud startgroup <Group>\n" .
                    CloudBridge::Prefix . "/cloud stopgroup <Group>\n" .
                    CloudBridge::Prefix . "/cloud send <Players> <Server>\n" .
                    CloudBridge::Prefix . "/cloud listServers\n" .
                    CloudBridge::Prefix . "/cloud listGroups\n" .
                    CloudBridge::Prefix . "/cloud notify <login|logout>\n";
                $sender->sendMessage($helpList);
                break;
        }
    }
}