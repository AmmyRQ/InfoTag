<?php

namespace AmmyRQ\InfoTag\Nametag;

use pocketmine\Server;
use pocketmine\plugin\Plugin;

class IntegrationManager
{

    /** @var Plugin|null */
    private static ?Plugin $currentRankManager = null;

    /** @var string[] */
    private static array $allowedRankManagers = ["PureChat", "RankSystem"];

    public static function init() : void
    {
        self::setRankManager();

        //Starts the task to update all nametags in the allowed worlds
        new UpdateNametagTask();
    }

    /**
     * @return Plugin|null
     */
    public static function getRankManager() : ?Plugin
    {
        return self::$currentRankManager;
    }


    /**
     * Sets which plugin will be used to obtain the custom nametag format
     * @return void
     */
    private static function setRankManager() : void
    {
        $pluginManager = Server::getInstance()->getPluginManager();

        if(!is_null($pluginManager->getPlugin("PureChat")) && !is_null($pluginManager->getPlugin("RankSystem")))
        {
            Server::getInstance()->getLogger()->warning("[InfoTag] PurePerms & RankSystem are enabled. Neither of the two plugins will be used. External nametag format support has been disabled.");
            return;
        }

        foreach(self::$allowedRankManagers as $plugins)
        {
            if(!is_null($pluginManager->getPlugin($plugins))) self::$currentRankManager = $pluginManager->getPlugin($plugins);
        }

        is_null(self::$currentRankManager) ?
            Server::getInstance()->getLogger()->notice("[InfoTag] No rank manager found. External nametag format support has been disabled.")
        :
            Server::getInstance()->getLogger()->notice("[InfoTag] \"" . self::$currentRankManager->getName() . "\" plugin selected as rank manager. External nametag format support has been enabled");
    }
}

