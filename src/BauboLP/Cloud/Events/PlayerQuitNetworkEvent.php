<?php


namespace BauboLP\Cloud\Events;


use pocketmine\event\Event;

/**
 * Class PlayerQuitNetworkEvent
 *
 * @package BauboLP\Cloud\Events
 *
 * This event is executed as soon as a player quit the network. More precisely, in the "PlayerDisconnectEvent".
 */

class PlayerQuitNetworkEvent extends Event
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