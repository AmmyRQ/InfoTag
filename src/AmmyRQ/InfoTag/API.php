<?php

namespace AmmyRQ\InfoTag;

use AmmyRQ\InfoTag\Factions\FactionsManager;
use pocketmine\Player;
use pocketmine\utils\Config;

class API
{

    /** @const float */
    private const CFG_VERSION = 1.0;

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

        if(FactionsManager::isFactionsSupportEnabled())
            $newNametag = str_replace("{faction}", FactionsManager::getFactionNameByPlayer($player), $newNametag);

        $player->setNameTag($newNametag);
    }

    /**
     * Cleans the player's nametag and restores it to its original format
     * @param Player $player
     * @return void
     */
    public static function resetNametag(Player $player) : void
    {
        if(Main::$isPureChatEnabled)
        {
            $plugin = Main::getInstance()->getServer()->getPluginManager()->getPlugin("PureChat");
            $nametag = $plugin->getNametag($player);
        }
        else $nametag = $player->getDisplayName();

        $player->setNameTag($nametag);
    }


    /**
     * Obtains the nametag format from the file format.yml
     * @return string
     */
    public static function getNametagFormat() : string
    {
        self::verifyFile();

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
        self::verifyFile();

        $file = new Config(Main::getInstance()->getDataFolder() . "format.yml", Config::YAML);

        if(is_null($file->get("update-time")) || !$file->exists("update-time")) return 20;

        return $file->get("update-time") * 20;
    }

    /**
     * Returns an array containing the name of all allowed worlds in which the nametag will be updated
     * @return array
     */
    public static function getAllowedWorlds() : array
    {
        self::verifyFile();

        $file = new Config(Main::getInstance()->getDataFolder() . "format.yml", Config::YAML);

        if(empty($file->get("worlds")) || is_null($file->get("worlds"))) return array();

        return $file->get("worlds");
    }

    /**
     * Returns the amount of ping formatted
     * @param Player $player
     * @return string
     */
    public static function getFormattedPing(Player $player) : string
    {
        self::verifyFile();

        $file = new Config(Main::getInstance()->getDataFolder() . "format.yml", Config::YAML);
        $ping = $player->getPing();

        if($ping >= 0 && $ping <= 90) return str_replace("{ping}", $ping, $file->get("pings")["good"] . " ms");
        if($ping >= 91 && $ping <= 140) return str_replace("{ping}", $ping,$file->get("pings")["intermediate"] . " ms");
        if($ping >= 141 && $ping <= 200) return str_replace("{ping}", $ping,$file->get("pings")["bad"] . " ms");
        if($ping >= 201) return str_replace("{ping}", $ping,$file->get("pings")["verybad"] . " ms");

        return "Unknown format.";
    }

    /**
     * Returns the name of the factions plugin to be used
     * @return string
     */
    public static function getFactionsPluginName() : string
    {
        self::verifyFile();

        $file = new Config(Main::getInstance()->getDataFolder() . "format.yml", Config::YAML);

        return $file->get("factions-plugin");
    }

    /**
     * Verifies the existence, version & content of the file "format.yml"
     * @return void
     */
    public static function verifyFile() : void
    {
        if(!is_dir(Main::getInstance()->getDataFolder())) @mkdir(Main::getInstance()->getDataFolder());

        if(!is_file(Main::getInstance()->getDataFolder() . "format.yml")) Main::getInstance()->saveResource("format.yml");

        $file = new Config(Main::getInstance()->getDataFolder() . "format.yml", Config::YAML);

        if(!$file->exists("cfg-version") || $file->get("cfg-version") !== self::CFG_VERSION)
        {
            Main::getInstance()->getServer()->getLogger()->debug("[InfoTag] The configuration version does not exists or is out of date in the format.yml file. Updating file...");

            @unlink(Main::getInstance()->getDataFolder() . "format.yml");
            Main::getInstance()->saveResource("format.yml");
        }

        if(!$file->exists("format"))
        {
            Main::getInstance()->getServer()->getLogger()->debug("[InfoTag] The content in the format.yml file does not exists or it is incomplete (\"format\" key). Updating file...");

            @unlink(Main::getInstance()->getDataFolder() . "format.yml");
            Main::getInstance()->saveResource("format.yml");
        }

        if(!$file->exists("worlds") || is_null($file->get("worlds")))
        {
            Main::getInstance()->getServer()->getLogger()->debug("[InfoTag] The content in the format.yml file does not exists or it is incomplete (\"world\" key). Updating file...");

            @unlink(Main::getInstance()->getDataFolder() . "format.yml");
            Main::getInstance()->saveResource("format.yml");
        }

        if(!$file->exists("factions-plugin"))
        {
            Main::getInstance()->getServer()->getLogger()->debug("[InfoTag] The content in the format.yml file does not exists or it is incomplete (\"factions-pluign\" key). Updating file...");

            @unlink(Main::getInstance()->getDataFolder() . "format.yml");
            Main::getInstance()->saveResource("format.yml");
        }
    }
}
