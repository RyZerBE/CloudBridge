<?php


namespace BauboLP\Cloud\Handler;


use BauboLP\Cloud\CloudBridge;
use BauboLP\Cloud\Events\CloudPacketSentFailedEvent;
use BauboLP\Cloud\Packets\DataPacket;
use pocketmine\utils\MainLogger;
use pocketmine\utils\TextFormat;

class RequestHandler extends \Thread
{
    /** @var string  */
    private $ip;
    /** @var int  */
    private $port;

    public $requestQueue;
    private $socket;

    private $stop;
    private $restart = false;

    public function __construct(string $ip, int $port = 7000)
    {
        $this->requestQueue = [];
        try {
            $this->port = $port;
            $this->ip = $ip;
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            $this->socket = $socket;
            socket_connect($socket, $ip, $port);
            socket_set_option($socket, SOL_TCP, TCP_NODELAY, 1);
            $this->start(PTHREADS_INHERIT_ALL);
            MainLogger::getLogger()->info(CloudBridge::Prefix . TextFormat::GREEN. "Verbindung mit der Cloud wurde hergestellt.");
        }catch (\Exception $e){
            MainLogger::getLogger()->critical(CloudBridge::Prefix . TextFormat::RED. "Verbindung mit der Cloud konnte NICHT hergestellt werden.");
            echo $e->getTraceAsString();
        }
    }

    public function writeData(string $encode, DataPacket $dataPacket = null) {
        try {
            socket_write($this->socket, $encode . PHP_EOL);
        } catch (\Exception $exception) {
            $ev = new CloudPacketSentFailedEvent($dataPacket);
            $ev->call();
            $this->restart = true;
        }
    }

    public function unsetRequest($buffer) {
        unset($this->requestQueue[$buffer]);
    }

    public function run()
    {

        while (!$this->stop) {
            $buffer = null;
            try {
                $buffer = @socket_read($this->socket, 65535, PHP_NORMAL_READ);
            } catch (\Exception $e){
                break;
            }
            if ($buffer != null) {
                $this->requestQueue[$buffer] = $buffer;
            }
            if (!$buffer) {
                break;
            }
        }

        if (!$this->stop) {
            $this->restart = true;
            socket_shutdown($this->socket);
            MainLogger::getLogger()->warning(CloudBridge::Prefix . TextFormat::YELLOW. "Verbindung zur Cloud verloren..");
        }
    }

    public function stop(bool $force = true)
    {
        $this->stop = true;
        if ($force) {


            try {
                socket_shutdown($this->socket);
            } catch (\Exception $exception) {}
        }
        MainLogger::getLogger()->warning(CloudBridge::Prefix . TextFormat::RED. "Verbindung zur Cloud beendet!");
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return mixed
     */
    public function getRestart()
    {
        return $this->restart;
    }

    /**
     * @return false|resource
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * @return mixed
     */
    public function getStop()
    {
        return $this->stop;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }
}