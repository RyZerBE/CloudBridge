<?php


namespace BauboLP\Cloud\Packets;


class InformationPacket extends DataPacket
{
    /**
     * @deprecated
     */
    public function __construct(string $message)
    {
        $this->data["message"] = $message;
        parent::__construct();
    }
}