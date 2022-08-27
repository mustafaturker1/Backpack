<?php

namespace Backpack;

use Backpack\command\BackpackCommand;
use Backpack\provider\SQLite;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;

class BACKPACK extends PluginBase
{
    /**
     * @var BACKPACK
     */
    private static BACKPACK $instance;

    /**
     * @return BACKPACK
     */
    public static function getInstance(): BACKPACK
    {
        return self::$instance;
    }

    protected function onEnable(): void
    {
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }
        self::$instance = $this;
        new SQLite();
        $this->getServer()->getCommandMap()->register("backpack", new BackpackCommand());
        $this->getLogger()->info("Backpack v1.0 Enable - https://github.com/mustafaturker1");
    }
}