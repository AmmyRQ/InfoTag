<?php

namespace AmmyRQ\InfoTag;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\plugin\PluginException;
use pocketmine\utils\Config;

use AmmyRQ\InfoTag\{API, UpdateNametagTask};

class Main extends PluginBase implements Listener
{
    /** @const float */
    private const CFG_VERSION = 1.0;

    /** @var bool */
    public static bool $isPureChatEnabled = false;

    /** @var null|self */
    private static ?Main $instance = null;

    /**
     * @return self
     * @throws PluginException if self::$instance is null
     */
    public static function getInstance() : self
    {
        if(!is_null(self::$instance)) return self::$instance;

        throw new PluginException("[InfoTag] Instance is null.");
    }

    /**
     * @return void
     */
    public function onEnable() : void
    {
        self::$instance = $this;
        $this->verifyFile();

        //Starts the nametag update task
        new UpdateNametagTask();

        $pluginManager = $this->getServer()->getPluginManager();

        //Checks if PureChat is enabled in order to use it in the plugin
        if(!is_null($pluginManager->getPlugin("PureChat")))
        {
            $this->getServer()->getLogger()->debug("[InfoTag] PureChat is enabled.");
            self::$isPureChatEnabled = true;
        }

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /**
     * Verifies the existence, version & content of the file "format.yml"
     * @return void
     */
    public function verifyFile() : void
    {
        if(!is_dir($this->getDataFolder())) @mkdir($this->getDataFolder());

        if(!is_file($this->getDataFolder() . "format.yml")) $this->saveResource("format.yml");

        $file = new Config($this->getDataFolder() . "format.yml", Config::YAML);

        if(!$file->exists("cfg-version") || $file->get("cfg-version") !== self::CFG_VERSION)
        {
            $this->getServer()->getLogger()->debug("[InfoTag] The configuration version does not exists or is out of date in the format.yml file. Updating file...");

            @unlink($this->getDataFolder() . "format.yml");
            $this->saveResource("format.yml");
        }

        if(!$file->exists("format"))
        {
            $this->getServer()->getLogger()->debug("[InfoTag] The content in the format.yml file does not exists or it is incomplete (\"format\" key). Updating file...");

            @unlink($this->getDataFolder() . "format.yml");
            $this->saveResource("format.yml");
        }

        if(!$file->exists("worlds") || is_null($file->get("worlds")))
        {
            $this->getServer()->getLogger()->debug("[InfoTag] The content in the format.yml file does not exists or it is incomplete (\"world\" key). Updating file...");

            @unlink($this->getDataFolder() . "format.yml");
            $this->saveResource("format.yml");
        }
    }

    /**
     * Records the player's name and device identifier in an array
     * @see API::$playerDevices
     * @param DataPacketReceiveEvent $event
     * @return void
     */
    public function onDataPacketReceiveEvent(DataPacketReceiveEvent $event) : void
    {
        if($event->getPacket() instanceof LoginPacket)
        {
            $file = new Config(Main::getInstance()->getDataFolder() . "format.yml", Config::YAML);

            //Checks if {device} exists in the format.yml file
            if(strpos($file->get("format"), "{device}"))
            {
                $name = $event->getPacket()->username;

                if(array_key_exists($name, API::$playerDevices)) unset(API::$playerDevices[$name]);
                API::$playerDevices[$name] = $event->getPacket()->clientData["DeviceOS"];
            }
        }
    }
}
