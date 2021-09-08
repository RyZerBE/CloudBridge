<?php


namespace BauboLP\Cloud\Packets;


use BauboLP\Cloud\CloudBridge;
use BauboLP\Cloud\Provider\CloudProvider;
use ReflectionClass;

abstract class DataPacket
{

    /** @var string  */
    private $password = "!wasfhuagbu89we!";
    /** @var array  */
    public $data = [];

    /**
     * DataPacket constructor.
     */
    public function __construct()
    {
        try {
            $className = (new ReflectionClass($this))->getShortName();
            $this->addData("packetName", $className);
            #var_dump($className);
        } catch (\ReflectionException $e) {}

        $this->data["serverName"] = CloudProvider::getServer();$this->data["gameServer"] = CloudProvider::getServer();
        $this->data["password"] = $this->getPassword();
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function addData(string $key, $value) {
        $this->data[$key] = $value;
    }

    /**
     * @param string $key
     */
    public function removeData(string $key) {
        unset($this->data[$key]);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getValue(string $key) {
        return $this->data[$key] ?? null;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getValue("packetName");
    }

    /**
     * @return false|string
     */
    public function encode() {
        return json_encode($this->data);
    }

    /**
     * @param string $data
     * @return mixed
     */
    public function decode(string $data) {
        $this->data = json_decode($data);
        return json_decode($data);
    }

    public function handle() {

    }
}