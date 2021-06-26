<?php


namespace BauboLP\Cloud\Events;


use BauboLP\Cloud\Client\Client;
use pocketmine\event\Event;

class ClientCreateEvent extends Event
{
    /** @var \BauboLP\Cloud\Client\Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return \BauboLP\Cloud\Client\Client
     */
    public function getClient(): \BauboLP\Cloud\Client\Client
    {
        return $this->client;
    }
}