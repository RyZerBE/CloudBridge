<?php


namespace BauboLP\Cloud\Task;


use BauboLP\Cloud\CloudBridge;
use BauboLP\Cloud\Packets\ServerTimingPacket;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\lang\TranslationContainer;
use pocketmine\Player;
use pocketmine\scheduler\BulkCurlTask;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\timings\TimingsHandler;
use pocketmine\utils\InternetException;

class TimingDelayPasteTask extends Task
{

    public function onRun(int $currentTick)
    {
        $fileTimings = fopen("php://temp", "r+b");
        TimingsHandler::printTimings($fileTimings);


        fseek($fileTimings, 0);
        $data = [
            "browser" => $agent = "RyZerBE-Cloud" . " " . Server::getInstance()->getPocketMineVersion(),
            "data" => $content = stream_get_contents($fileTimings)
        ];
        fclose($fileTimings);

        $host = Server::getInstance()->getProperty("timings.host", "timings.pmmp.io");

        Server::getInstance()->getAsyncPool()->submitTask(new class(new ConsoleCommandSender(), $host, $agent, $data) extends BulkCurlTask {
            /** @var string */
            private $host;

            /**
             * @param \pocketmine\command\CommandSender $sender
             * @param string $host
             * @param string $agent
             * @param string[] $data
             * @phpstan-param array<string, string> $data
             */
            public function __construct(CommandSender $sender, string $host, string $agent, array $data)
            {
                parent::__construct([
                    [
                        "page" => "https://$host?upload=true",
                        "extraOpts" => [
                            CURLOPT_HTTPHEADER => [
                                "User-Agent: $agent",
                                "Content-Type: application/x-www-form-urlencoded"
                            ],
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => http_build_query($data),
                            CURLOPT_AUTOREFERER => false,
                            CURLOPT_FOLLOWLOCATION => false
                        ]
                    ]
                ], $sender);
                $this->host = $host;
            }

            public function onCompletion(Server $server)
            {
                $sender = $this->fetchLocal();
                if ($sender instanceof Player and !$sender->isOnline()) {
                    return;
                }
                $result = $this->getResult()[0];
                if ($result instanceof InternetException) {
                    $server->getLogger()->logException($result);
                    return;
                }
                if (isset($result[0]) && is_array($response = json_decode($result[0], true)) && isset($response["id"])) {
                    $packet = new ServerTimingPacket();
                    $packet->addData("timing", "https://" . $this->host . "/?id=" . $response["id"]);
                    CloudBridge::getInstance()->getClient()->getPacketHandler()->writePacket($packet);
                }
            }
        });
    }
}