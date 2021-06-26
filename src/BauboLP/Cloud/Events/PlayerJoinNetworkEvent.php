<?php


namespace BauboLP\Cloud\Events;


use pocketmine\event\Event;

/**
 * Class PlayerJoinNetworkEvent
 *
 * @package BauboLP\Cloud\Events
 *
 * This event is executed as soon as a player enters the network. More precisely, in the "PostLoginEvent".
 */

class PlayerJoinNetworkEvent extends Event
{
    /**
     * @var  string
     */
    private $playerName;

    public function __construct(string $playerName)
    {
        $this->playerName = $playerName;
    }


    /**
     * @return string
     */
    public function getPlayerName(): string
    {
        return $this->playerName;
    }
}