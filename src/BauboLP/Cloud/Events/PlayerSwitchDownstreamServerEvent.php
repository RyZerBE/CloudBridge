<?php


namespace BauboLP\Cloud\Events;


use pocketmine\event\Event;
use pocketmine\Player;

class PlayerSwitchDownstreamServerEvent extends Event
{
    /**
     * Class PlayerQuitNetworkEvent
     *
     * @package BauboLP\Cloud\Events
     *
     * This event is executed as soon as a player switch downstream servers of WaterdogPE.
     */

    /** @var \pocketmine\Player  */
    private $player;
    /** @var string */
    private $downstreamServerName;

    public function __construct(Player $player, string $downstreamServerName)
    {
        $this->player = $player;
        $this->downstreamServerName = $downstreamServerName;
    }

    /**
     * @return \pocketmine\Player
     */
    public function getPlayer(): \pocketmine\Player
    {
        return $this->player;
    }

    /**
     * @return string
     */
    public function getDownstreamServerName(): string
    {
        return $this->downstreamServerName;
    }
}