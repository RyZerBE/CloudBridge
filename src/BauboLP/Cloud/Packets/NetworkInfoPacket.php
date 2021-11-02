<?php

namespace BauboLP\Cloud\Packets;

use function var_dump;

class NetworkInfoPacket extends DataPacket {

    public function handle(){
        #var_dump($this->data);
    }
}