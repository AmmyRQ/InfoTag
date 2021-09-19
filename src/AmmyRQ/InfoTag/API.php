<?php

namespace AmmyRQ\InfoTag;

use pocketmine\Player;
use pocketmine\utils\Config;

class API
{

    /** @var array */
    private static array $devices = [
      "Unknown", "Android", "iOS", "MacOS", "FireOS",
      "GearVR", "HoloLens", "Windows10", "Windows",
      "Educal", "Dedicated", "PS4", "Switch", "Xbox"
    ];

    /** @var array */
    public static array $playerDevices = [];

    /**
     * Updates the player's nametag
     * @param Player $player
     * @return void
     */
    public static function updatePlayerTag(Player $player) : void
    {
        if(Main::$isPureChatEnabled)
        {
            $plugin = Main::getInstance()->getServer()->getPluginManager()->getPlugin("PureChat");
            $nametag = $plugin->getNametag($player);
        }
        else $nametag = $player->getDisplayName();

        $newNametag = str_replace(
            ["{nametag}", "{health}", "{maxhealth}", "{food}", "{maxfood}", "{ping}"],
            [$nametag, $player->getHealth(), $player->getMaxHealth(), $player->getFood(), $player->getMaxFood(), self::getFormattedPing($player)],
            self::getNametagFormat()
        );

        if(array_key_exists($player->getName(), self::$playerDevices))
            $newNametag = str_replace("{device}", self::$devices[self::$playerDevices[$player->getName()]], $newNametag);

        $player->setNameTag($newNametag);
    }


    /**
     * Obtains the nametag format from the file format.yml
     * @return string
     */
    public static function getNametagFormat() : string
    {
        Main::getInstance()->verifyFile();

        $file = new Config(Main::getInstance()->getDataFolder() . "format.yml", Config::YAML);

        if($file->get("format") === null) return "";
        return $file->get("format");
    }

    /**
     * Returns the amount of ticks at which the task will be updated (@see UpdateNametagTask::class)
     * @return int
     */
    public static function getUpdateTicks() : int
    {
        Main::getInstance()->verifyFile();

        $file = new Config(Main::getInstance()->getDataFolder() . "format.yml", Config::YAML);

        if(is_null($file->get("update-time")) || !$file->exists("update-time")) return 20;

        return $file->get("update-time") * 20;
    }

    /**
     * Returns an array containing the name of all allowed worlds in which the nametag will be updated
     * @return array
     */
    public static function getAllowedWorld() : array
    {
        Main::getInstance()->verifyFile();

        $file = new Config(Main::getInstance()->getDataFolder() . "format.yml", Config::YAML);

        if(empty($file->get("worlds")) || is_null($file->get("worlds"))) return array();

        return $file->get("worlds");
    }

    /**
     * Returns the amount of ping formatted
     * @return string
     */
    public static function getFormattedPing(Player $player) : string
    {
        Main::getInstance()->verifyFile();

        $file = new Config(Main::getInstance()->getDataFolder() . "format.yml", Config::YAML);
        $ping = $player->getPing();

        if($ping >= 0 && $ping <= 90) return str_replace("{ping}", $ping, $file->get("pings")["good"] . " ms");
        if($ping >= 91 && $ping <= 140) return str_replace("{ping}", $ping,$file->get("pings")["intermediate"] . " ms");
        if($ping >= 141 && $ping <= 200) return str_replace("{ping}", $ping,$file->get("pings")["bad"] . " ms");
        if($ping >= 201) return str_replace("{ping}", $ping,$file->get("pings")["verybad"] . " ms");

        return "Unknown format.";
    }
}
