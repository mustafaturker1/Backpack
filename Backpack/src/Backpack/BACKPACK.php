<?php

namespace Backpack;

use Generator;
use Backpack\command\BackpackCommand;
use Backpack\executors\CustomExecutor;
use Backpack\global\MyGlobalExecutor;
use Illuminate\Database\Capsule\Manager;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use SOFe\AwaitGenerator\Await;
use Webmozart\PathUtil\Path;
use xerenahmed\database\ExecutorManager;

require_once 'vendor\autoload.php';

class BACKPACK extends PluginBase
{
    public const CONN_NAME = "backpack-plugin";

    /**
     * @var BACKPACK
     */
    private static BACKPACK $instance;

    private ExecutorManager $executorManager;

    /**
     * @return BACKPACK
     */
    public static function getInstance(): BACKPACK
    {
        return self::$instance;
    }

    protected function onEnable(): void
    {
        $capsule = self::newCapsule(Server::getInstance()->getDataPath());
        ExecutorManager::registerCapsule(self::CONN_NAME, $capsule);

        $this->executorManager = ExecutorManager::create();
        $this->executorManager->register(MyGlobalExecutor::getInstance());
        $this->executorManager->register(CustomExecutor::getInstance());

        Server::getInstance()->getLogger()->info("Creating tables...");
        Await::f2c(function (): Generator {
            yield from CustomExecutor::getInstance()->createTables();
        });


        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
        self::$instance = $this;
        $this->getServer()->getCommandMap()->register("backpack", new BackpackCommand());
        $this->getLogger()->info("Backpack v2.0 Enable - https://github.com/mustafamikaelson");
    }

    public function onDisable(): void{
        $this->executorManager->quit();
    }

    public static function newCapsule(string $dataPath): Manager{
        return ExecutorManager::newCapsule(self::CONN_NAME, [
            "driver" => "sqlite",
            "database" => Path::join($dataPath, "backpack.sqlite"),
            "prefix" => "",
        ]);
    }
}