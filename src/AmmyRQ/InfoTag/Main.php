<?php

namespace AmmyRQ\InfoTag;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\plugin\PluginException;
use pocketmine\utils\Config;

use AmmyRQ\InfoTag\{API, UpdateNametagTask};
use AmmyRQ\InfoTag\Factions\FactionsManager;

class Main extends PluginBase implements Listener
{

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
        API::verifyFile();

        //Starts the nametag update task
        new UpdateNametagTask();
        
        //Initialises factions manager
        FactionsManager::init();

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
     * Records the player's name and device identifier in an array
     * @see API::$playerDevices
     * @param DataPacketReceiveEvent $event
     * @return void
     */
    public function onDataPacketReceiveEvent(DataPacketReceiveEvent $event) : void
    {
        if($event->getPacket() instanceof LoginPacket)
        {
            $file = new Config(self::getInstance()->getDataFolder() . "format.yml", Config::YAML);

            //Checks if {device} exists in the format.yml file
            if(strpos($file->get("format"), "{device}"))
            {
                $name = $event->getPacket()->username;

                if(array_key_exists($name, API::$playerDevices)) unset(API::$playerDevices[$name]);
                API::$playerDevices[$name] = $event->getPacket()->clientData["DeviceOS"];
            }
        }
    }

    /**
     * Resets the player's nametag format if the player travels to a world where the info nametag is not allowed
     * @param EntityLevelChangeEvent $event
     * @return void
     */
    public function onEntityLevelChangeEvent(EntityLevelChangeEvent $event) : void
    {
        $player = $event->getEntity();

        if($player instanceof Player)
        {
            if(!in_array($event->getTarget()->getName(), API::getAllowedWorlds()))
                API::resetNametag($player);
        }
    }
}
