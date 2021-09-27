<?php

namespace AmmyRQ\InfoTag\Nametag;

use pocketmine\scheduler\Task;

use AmmyRQ\InfoTag\{Main, API};

class UpdateNametagTask extends Task
{

    public function __construct()
    {
        Main::getInstance()->getScheduler()->scheduleRepeatingTask($this, API::getUpdateTicks());
    }

    /**
     * @param int|null $currentTick
     * @return void
     */
    public function onRun(?int $currentTick) : void
    {
        foreach(API::getAllowedWorlds() as $worldName)
        {
            if(!is_null($level = Main::getInstance()->getServer()->getLevelByName($worldName)) && Main::getInstance()->getServer()->isLevelLoaded($worldName))
            {
                foreach($level->getPlayers() as $players) API::updatePlayerTag($players);
            }
        }
    }
}
