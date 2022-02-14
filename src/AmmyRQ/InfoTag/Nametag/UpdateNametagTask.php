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
     * @return void
     */
    public function onRun() : void
    {
        foreach(API::getAllowedWorlds() as $worldName)
        {
            if(!is_null($level = Main::getInstance()->getServer()->getWorldManager()->getWorldByName($worldName)) && Main::getInstance()->getServer()->getWorldManager()->isWorldLoaded($worldName))
            {
                foreach($level->getPlayers() as $players)
                    API::updatePlayerTag($players);
            }
        }
    }
}
