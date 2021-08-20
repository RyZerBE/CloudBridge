<?php


namespace BauboLP\Cloud\Provider;


use BauboLP\Cloud\CloudBridge;
use BauboLP\Cloud\Events\PlayerSwitchDownstreamServerEvent;
use BauboLP\Cloud\Packets\CloudReloadPacket;
use BauboLP\Cloud\Packets\CreateServerPacket;
use BauboLP\Cloud\Packets\DispatchProxyCommandPacket;
use BauboLP\Cloud\Packets\PlayerMoveServerPacket;
use BauboLP\Cloud\Packets\StartGroupPacket;
use BauboLP\Cloud\Packets\StopGroupPacket;
use BauboLP\Cloud\Packets\StopServerPacket;
use pocketmine\Server;
use pocketmine\utils\Config;
use function in_array;

class CloudProvider
{
    /** @var \pocketmine\utils\Config  */
    private $groupConfig;

    public function __construct()
    {
        $this->groupConfig = new Config("/root/RyzerCloud/config.json", Config::JSON);
        $this->groupConfig->reload();
    }

    /**
     * @return string
     */
    public static function getServer(): string
    {
        return Server::getInstance()->getMotd();
    }

    /**
     * @return bool
     */
    public static function existCrashDumps(): bool
    {
        return count(glob(Server::getInstance()->getDataPath()."crashdumps/*")) > 0;
    }

    public static function saveCrashDumps(): void
    {
        popen("cp -r " . Server::getInstance()->getDataPath() . "crashdumps/* " . "/root/RyzerCloud/crashdumps/", 'r');
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        $groups = [];
        foreach ($this->getGroupConfig()->get('groups') as $data) {
                $groups[] = $data['groupName'];
        }
        return $groups;
    }

    /**
     * @param string $group
     * @param int $count
     * @param bool $callback
     */
    public function startServer(string $group, int $count = 1, bool $callback = false): void
    {
        $pk = new CreateServerPacket();
        $pk->addData("groupName", $group);
        $pk->addData("count", $count);
        CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($pk);
    }

    /**
     * @param string $server
     */
    public function stopServer(string $server): void
    {
        $packet = new StopServerPacket();
        $packet->addData("serverName", $server);
        CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($packet);
    }

    /**
     * @param string $group
     */
    public function startGroup(string $group): void
    {
        $packet = new StartGroupPacket();
        $packet->addData("groupName", $group);
        CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($packet);
    }

    /**
     * @param string $group
     */
    public function stopGroup(string $group): void
    {
        $packet = new StopGroupPacket();
        $packet->addData("groupName", $group);
        CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($packet);
    }

    public function reloadCloud(): void
    {
        $packet = new CloudReloadPacket();
        CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($packet);
    }

    /**
     * @return \pocketmine\utils\Config
     */
    public function getGroupConfig(): \pocketmine\utils\Config
    {
        return $this->groupConfig;
    }

    /**
     * @param string $group
     * @return bool
     */
    public function existGroup(string $group): bool
    {
        foreach ($this->getGroupConfig()->get('groups') as $data) {
            if($data['groupName'] == $group) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $group
     * @return int
     */
    public function getMaxRunningServer(string $group): int
    {
        if(!$this->existGroup($group)) return 0;
        foreach ($this->getGroupConfig()->get('groups') as $data) {
            if($data['groupName'] == $group) {
                return $data['maxRunServer'];
            }
        }
        return 0;
    }

    /**
     * @param string $group
     * @return int
     */
    public function getMaxPlayersToStartNewServer(string $group): int
    {
        if(!$this->existGroup($group)) return 10000;

        if(!$this->existGroup($group)) return 0;
        foreach ($this->getGroupConfig()->get('groups') as $data) {
            if($data['groupName'] == $group) {
                return $data['startNewServerByPlayers'];
            }
        }
        return 100000;
    }

    /**
     * @return array
     */
    public function getRunningServers(): array
    {
        $servers = scandir("/root/RyzerCloud/servers");
        return $servers;
    }

    /**
     * @param string $group
     * @return array
     */
    public function getRunningServersByGroup(string $group): array
    {
        $runningServers = [];
        foreach (glob("/root/RyzerCloud/servers/$group-*/server.properties") as $servers) {
            $c = new Config("$servers");
            $server = $c->get('motd');
            $runningServers[] = $server;
        }
        return $runningServers;
    }

    /**
     * @param string $serverName
     * @return int|null
     */
    public function getServerPort(string $serverName): ?int {
        if(!in_array($serverName, $this->getRunningServers())) return null;
        $c = new Config("/root/RyzerCloud/servers/$serverName/server.properties");
        return $c->get("server-port", null);
    }

    /**
     * @param string $serverName
     * @return bool|string
     */
    public function isServerPrivate(string $serverName) {

        if(!in_array($serverName, $this->getRunningServers())) return false;

        $c = new Config("/root/RyzerCloud/servers/$serverName/server.properties");
        $owner = $c->get("private");
        if($owner != "off" && $owner != "" && strlen($owner) > 0) {
            return (string)$owner;
        }
        return (bool)false;
    }

    /**
     * @param string $group
     * @return bool
     */
    public function canGroupBePrivate(string $group): bool {

        if(!$this->existGroup($group)) return false;

        foreach ($this->getGroupConfig()->get('groups') as $data) {
            if($data['groupName'] == $group) {
                return (bool)$data['canBePrivate'];
            }
        }
        return false;
    }

    /**
     * @return int
     */
    public function getRunningServerCount(): int
    {
        return count(glob("/root/RyzerCloud/servers/*"));
    }

    /**
     * @param string $serverName
     */
    public function addServerToBlackList(string $serverName): void
    {
        if(!is_dir("/root/RyzerCloud/servers/blacklist/$serverName")) {
            mkdir("/root/RyzerCloud/servers/blacklist/$serverName");
        }
    }

    /**
     * @param string $serverName
     */
    public function removeServerFromBlackList(string $serverName): void
    {
        if(is_dir("/root/RyzerCloud/servers/blacklist/$serverName")) {
            popen("rm -r /root/RyzerCloud/servers/blacklist/$serverName", 'r');
        }
    }

    /**
     *
     * @param string $serverName
     * @return bool
     */
    public function isServerBlacklisted(string $serverName)
    {
        return is_dir("/root/RyzerCloud/servers/blacklist/$serverName");
    }

    /**
     * Execute proxy commands
     *
     * @param string $playerName
     * @param string $commandLine - without slash!
     */
    public function dispatchProxyCommand(string $playerName, string $commandLine)
    {
        $pk = new DispatchProxyCommandPacket();
        $pk->addData("commandLine", $commandLine);
        $pk->addData("playerName", $playerName);
        CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($pk);
    }

    /**
     * Transfer players between two WaterdogPE servers
     *
     * @param array $players
     * @param string $server
     */
    public function transferPlayer(array $players, string $server)
    {
        $players2 = "";

        $players2 = implode(":", $players);

        foreach ($players as $player) {
            if(($onlinePlayer = Server::getInstance()->getPlayerExact($player)) != null) {
                $ev = new PlayerSwitchDownstreamServerEvent($onlinePlayer, $server);
                $ev->call();
            }
        }

        $pk = new PlayerMoveServerPacket();
        $pk->addData("serverName", $server);
        $pk->addData("playerNames", $players2);
        CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($pk);
    }
    /**
     * @param array $players
     * @param string $server
     */
    public function sendPlayer(array $players, string $server)
    {
        $players2 = "";

        foreach ($players as $player) $players2 .= ":$player";

        $pk = new PlayerMoveServerPacket();
        $pk->addData("serverName", $server);
        $pk->addData("playerNames", $players2);
        CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($pk);
    }
}