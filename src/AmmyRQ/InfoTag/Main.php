<?php

namespace AmmyRQ\InfoTag;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\plugin\PluginException;
use pocketmine\utils\Config;

use AmmyRQ\InfoTag\Factions\FactionsManager;
use AmmyRQ\InfoTag\Nametag\IntegrationManager;

class Main extends PluginBase implements Listener
{

    /** @var null|Main */
    private static ?Main $instance = null;

    /**
     * @return self
     * @throws PluginException if self::$instance is null
     */
    public static function getInstance() : Main
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
        
        //Initialises factions manager
        FactionsManager::init();

        //Initialises nametag manager
        IntegrationManager::init();

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /**
     * Records the player's name and device identifier in an array
     * @see API::$playerDevices
     * @param PlayerJoinEvent $event
     * @return void
     */
    public function onJoin(PlayerJoinEvent $event) : void
    {
        $deviceOs = $event->getPlayer()->getPlayerInfo()->getExtraData()["DeviceOS"];

        $file = new Config(self::getInstance()->getDataFolder() . "format.yml", Config::YAML);

        //Checks if {device} exists in the format.yml file
        if(strpos($file->get("format"), "{device}"))
        {
            $name = $event->getPlayer()->getName();

            if(array_key_exists($name, API::$playerDevices))
                unset(API::$playerDevices[$name]);

            API::$playerDevices[$name] = $deviceOs;
        }
    }

    /**
     * Resets the player's nametag format if the player travels to a world where the info nametag is not allowed
     * @param EntityTeleportEvent $event
     * @return void
     */
    public function onEntityTeleport(EntityTeleportEvent $event) : void
    {
        $entity = $event->getEntity();

        if($entity instanceof Player)
        {
            if(!in_array($event->getTo()->getWorld()->getDisplayName(), API::getAllowedWorlds()))
                API::resetNametag($entity);
        }
    }
}
