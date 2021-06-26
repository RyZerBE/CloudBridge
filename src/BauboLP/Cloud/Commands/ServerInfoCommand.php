<?php


namespace BauboLP\Cloud\Commands;


use BauboLP\Cloud\CloudBridge;
use BauboLP\Cloud\Provider\CloudProvider;
use baubolp\core\provider\LanguageProvider;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class ServerInfoCommand extends Command
{

    public function __construct()
    {
        parent::__construct('serverinfo', "", "", ['si']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $sender->sendMessage(CloudBridge::Prefix.LanguageProvider::getMessageContainer('server-info', $sender->getName(), ['#servername' => CloudProvider::getServer()]));
    }
}