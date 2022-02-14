<?php

namespace AmmyRQ\InfoTag\Factions;

use pocketmine\Server;
use pocketmine\player\Player;

use AmmyRQ\InfoTag\API;

class FactionsManager
{

    /** @var bool */
    private static bool $factionsSupport = false;

    /** @var string */
    private static string $currentFactionsPlugin = "";

    /**
     * Defines which factions plugins are supported
     * @var string[]
     */
    private static array $supportedFactionsPlugins = ["SimpleFaction", "FactionMaster", "PiggyFactions", "FactionsPE"];

    /**
     * Verifies which factions plugin will be used, and if it is valid to enable factions support
     * @return void
     */
    public static function init() : void
    {
        $pluginManager = Server::getInstance()->getPluginManager();
        $factionsPlugin = API::getFactionsPluginName();

        if(in_array($factionsPlugin, self::$supportedFactionsPlugins)) {
            if (!is_null($pluginManager->getPlugin($factionsPlugin)))
            {
                self::$currentFactionsPlugin = $factionsPlugin;
                self::$factionsSupport = true;
                Server::getInstance()->getLogger()->notice("[InfoTag] Factions support has been enabled. Using \"$factionsPlugin\" plugin.");
            }
            else
                Server::getInstance()->getLogger()->warning("[InfoTag] \"$factionsPlugin\" plugin is not installed or is disabled. Factions support has been disabled.");
        }
        else
            Server::getInstance()->getLogger()->warning("[InfoTag] No valid factions plugin has been provided. Factions support has been disabled.");
    }

    /**
     * @return bool
     */
    public static function isFactionsSupportEnabled() : bool
    {
        return self::$factionsSupport;
    }

    /**
     * @return string
     */
    public static function getFactionsPluginName() : string
    {
        return self::$currentFactionsPlugin;
    }

    /**
     * @param Player $player
     * @return string
     */
    public static function getFactionNameByPlayer(Player $player) : string
    {
        $factionName = null;

        switch(self::getFactionsPluginName())
        {
            case "SimpleFaction":
                /** @link https://github.com/AyzrixYTB/SimpleFaction/blob/master/src/Ayzrix/SimpleFaction/API/FactionsAPI.php */
                 $factionName = \Ayzrix\SimpleFaction\API\FactionsAPI::getFaction($player->getName());
            break;

            case "FactionMaster":
                /** @link https://github.com/ShockedPlot7560/FactionMaster/blob/stable/src/ShockedPlot7560/FactionMaster/API/MainAPI.php */
                $factionObject = \ShockedPlot7560\FactionMaster\API\MainAPI::getFactionOfPlayer($player->getName());

                if(!is_null($factionObject)) $factionName = $factionObject->name;
            break;

            case "PiggyFactions":
                $plugin = Server::getInstance()->getPluginManager()->getPlugin("PiggyFactions");
                $faction = $plugin->getPlayerManager()->getPlayerFaction($player->getUniqueId());

                if(!is_null($faction)) $factionName = $faction->getName();
            break;

            case "FactionsPE":
                /** @link https://github.com/BlockHorizons/FactionsPE/blob/reborn/src/BlockHorizons/FactionsPE/manager/Members.php */
                $factionObject = \BlockHorizons\FactionsPE\manager\Members::get($player)->getFaction();
                $factionName = $factionObject->getName();
            break;
        }

        if($factionName === "" || is_null($factionName)) $factionName = "No faction";

        return $factionName;
    }
}
