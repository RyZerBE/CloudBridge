<?php


namespace BauboLP\Cloud\Packets;


class InformationPacket extends DataPacket
{

    public function __construct(string $message)
    {
        $this->data["message"] = $message;
        parent::__construct();
    }
}