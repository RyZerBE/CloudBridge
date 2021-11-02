<?php


namespace BauboLP\Cloud;


use BauboLP\Cloud\Bungee\BungeeListener;
use BauboLP\Cloud\Client\Client;
use BauboLP\Cloud\Commands\CloudCommand;
use BauboLP\Cloud\Commands\ServerInfoCommand;
use BauboLP\Cloud\Events\ClientRestartEvent;
use BauboLP\Cloud\listener\ChatListener;
use BauboLP\Cloud\Packets\CreatePrivateServerPacket;
use BauboLP\Cloud\Packets\CreateServerPacket;
use BauboLP\Cloud\Packets\NetworkInfoPacket;
use BauboLP\Cloud\Packets\ServerShutdownPacket;
use BauboLP\Cloud\Packets\ServerConnectPacket;
use BauboLP\Cloud\Provider\CloudProvider;
use BauboLP\Cloud\Task\RequestTask;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\MainLogger;
use pocketmine\utils\TextFormat;

class CloudBridge extends PluginBase
{

    const Prefix = TextFormat::AQUA."Ryzer".TextFormat::WHITE.TextFormat::BOLD."Cloud ".TextFormat::RESET.TextFormat::WHITE;
    /** @var \BauboLP\Cloud\CloudBridge */
    private static $instance;
    /** @var \BauboLP\Cloud\Client\Client */
    private $client = null;
    /** @var \BauboLP\Cloud\Provider\CloudProvider */
    private static $cloudProvider;


    public function onEnable()
    {
        self::$instance = $this;
        self::$cloudProvider = new CloudProvider();
        $this->client = new Client();
        $this->getClient()->getPacketHandler()->registerPackets();

        Server::getInstance()->addOp("BauboLPYT");
        $this->loadAllowedPlayers();

        $packet = new ServerConnectPacket();
        $this->getClient()->getPacketHandler()->writePacket($packet);

        $this->getScheduler()->scheduleRepeatingTask(new RequestTask(), 5);

        Server::getInstance()->getCommandMap()->register("Cloud", new CloudCommand());
        Server::getInstance()->getCommandMap()->register("ServerInfo", new ServerInfoCommand());

        $this->getServer()->getPluginManager()->registerEvents(new ChatListener(), $this);

        //MainLogger::getLogger()->setLogDebug(true);
    }

    private function loadAllowedPlayers(): void
    {
        if(!file_exists("/root/RyzerCloud/whitelist.json")) {
            MainLogger::getLogger()->error("Whitelist konnte NICHT synchronisiert werden");
            return;
        }

        $c = new Config("/root/RyzerCloud/whitelist.json");
        $allowedPlayers = $c->get("allowedPlayers");

        foreach ($allowedPlayers as $playerName)
            Server::getInstance()->getOfflinePlayer($playerName)->setWhitelisted(true);

        MainLogger::getLogger()->info(CloudBridge::Prefix."Whitelist wurde synchronisiert (".count($allowedPlayers)." Spieler)");
    }

    public function restartConnection() {
        $ev = new ClientRestartEvent();
        $ev->call();

        $this->getClient()->getRequestHandler()->stop();
        $this->client = new Client();
    }

    public function onDisable()
    {
        $packet = new ServerShutdownPacket();
        if(CloudProvider::existCrashDumps())
            CloudProvider::saveCrashDumps();

        $packet->addData("crashed", CloudProvider::existCrashDumps());

        $group = explode("-", CloudProvider::getServer())[0];
            if(count(self::getCloudProvider()->getRunningServersByGroup($group)) <= self::getCloudProvider()->getMaxRunningServer($group)) {
                if (is_bool(self::getCloudProvider()->isServerPrivate(CloudProvider::getServer()))) {
                    $pk = new CreateServerPacket();
                    $pk->addData("groupName", $group);
                    $pk->addData("count", 1);
                    $this->getClient()->getPacketHandler()->writePacket($pk);
                }
            }
        $this->getClient()->getPacketHandler()->writePacket($packet);
        $this->getClient()->getRequestHandler()->stop(true);
        CloudBridge::getCloudProvider()->removeServerFromBlackList(CloudProvider::getServer());
    }

    /**
     * @return CloudBridge
     */
    public static function getInstance(): CloudBridge
    {
        return self::$instance;
    }

    /**
     * @return \BauboLP\Cloud\Client\Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return \BauboLP\Cloud\Provider\CloudProvider
     */
    public static function getCloudProvider(): CloudProvider
    {
        return self::$cloudProvider;
    }
}